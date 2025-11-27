<?php
include '../components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
   $delete_id = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);

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
