<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if(!$admin_id){
   header('location:admin_login.php');
   exit;
}

// ✅ Agregar producto
if(isset($_POST['add_product'])){
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
   $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);

   // Manejo de imágenes
   $images = [];
   for ($i = 1; $i <= 3; $i++) {
      $img_name = $_FILES["image_0$i"]['name'];
      $img_name = filter_var($img_name, FILTER_SANITIZE_STRING);
      $img_tmp = $_FILES["image_0$i"]['tmp_name'];
      $img_size = $_FILES["image_0$i"]['size'];

      if($img_size > 2000000){
         $message[] = "La imagen $i supera el tamaño permitido (2 MB)";
      } else {
         $folder = '../uploaded_img/'.$img_name;
         move_uploaded_file($img_tmp, $folder);
         $images[] = $img_name;
      }
   }

   // Validar duplicados
   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if($select_products->rowCount() > 0){
      $message[] = '¡El nombre del producto ya existe!';
   } else {
      $insert_products = $conn->prepare("
         INSERT INTO `products`(name, details, price, image_01, image_02, image_03)
         VALUES(?,?,?,?,?,?)
      ");
      $insert_products->execute([$name, $details, $price, $images[0] ?? '', $images[1] ?? '', $images[2] ?? '']);
      $message[] = '¡Nuevo producto añadido correctamente!';
   }
}

// ✅ Eliminar producto
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];

   // Borrar imágenes asociadas
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);

   foreach (['image_01', 'image_02', 'image_03'] as $img_col) {
      if(!empty($fetch_delete_image[$img_col]) && file_exists('../uploaded_img/'.$fetch_delete_image[$img_col])){
         unlink('../uploaded_img/'.$fetch_delete_image[$img_col]);
      }
   }

   // Borrar registros asociados
   $conn->prepare("DELETE FROM `products` WHERE id = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM `cart` WHERE pid = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?")->execute([$delete_id]);

   header('location:products.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Gestión de Productos | Petals by Montse</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="add-products">

   <h1 class="heading">Agregar nuevo producto</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <div class="flex">

         <div class="inputBox">
            <span>Nombre del producto:</span>
            <input type="text" name="name" class="box" required maxlength="100" placeholder="Ej: Rosa encantada en vidrio">
         </div>

         <div class="inputBox">
            <span>Precio del producto:</span>
            <input type="number" name="price" min="0" max="999999" class="box" required placeholder="Ej: 29.99">
         </div>

         <div class="inputBox">
            <span>Imagen principal:</span>
            <input type="file" name="image_01" accept="image/*" class="box" required>
         </div>

         <div class="inputBox">
            <span>Imagen secundaria:</span>
            <input type="file" name="image_02" accept="image/*" class="box">
         </div>

         <div class="inputBox">
            <span>Imagen adicional:</span>
            <input type="file" name="image_03" accept="image/*" class="box">
         </div>

         <div class="inputBox" style="flex-basis:100%;">
            <span>Descripción del producto:</span>
            <textarea name="details" class="box" maxlength="500" required placeholder="Escriba una descripción breve del producto..." rows="5"></textarea>
         </div>

      </div>
      <input type="submit" value="Añadir producto" class="btn" name="add_product">
   </form>
</section>

<section class="show-products">
   <h1 class="heading">Productos en catálogo</h1>
   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC");
      $select_products->execute();

      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <div class="box">
      <img src="../uploaded_img/<?= htmlspecialchars($fetch_products['image_01']); ?>" alt="Imagen del producto">
      <h3 class="name"><?= htmlspecialchars($fetch_products['name']); ?></h3>
      <p class="price">$<?= number_format($fetch_products['price'], 2); ?></p>
      <p class="details"><?= htmlspecialchars($fetch_products['details']); ?></p>

      <div class="flex-btn">
         <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Editar</a>
         <a href="products.php?delete=<?= $fetch_products['id']; ?>" 
            class="delete-btn" 
            onclick="return confirm('¿Deseas eliminar este producto y sus imágenes asociadas?');">
            Eliminar
         </a>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">Aún no hay productos registrados.</p>';
      }
   ?>
   </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
