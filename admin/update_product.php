<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
   header('location:admin_login.php');
   exit;
}

// 🔹 Actualización del producto
if (isset($_POST['update'])) {
   $pid     = $_POST['pid'];
   $name    = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
   $price   = floatval($_POST['price']);
   $details = htmlspecialchars(trim($_POST['details']), ENT_QUOTES, 'UTF-8');

   // 🟢 Actualizar información básica
   $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, details = ? WHERE id = ?");
   $update_product->execute([$name, $price, $details, $pid]);
   $message[] = '✅ Producto actualizado correctamente.';

   // 🖼️ Procesar imágenes nuevas
   $image_fields = ['image_01', 'image_02', 'image_03'];

   foreach ($image_fields as $index => $img_field) {
      $old_image = $_POST['old_' . $img_field];
      if (!empty($_FILES[$img_field]['name'])) {

         // Nuevo nombre seguro
         $new_image_name = time() . '_' . basename($_FILES[$img_field]['name']);
         $new_image_name = htmlspecialchars(trim($new_image_name), ENT_QUOTES, 'UTF-8');
         $tmp_name = $_FILES[$img_field]['tmp_name'];
         $size = $_FILES[$img_field]['size'];
         $folder = '../uploaded_img/' . $new_image_name;

         // Validar tamaño
         if ($size > 2000000) {
            $message[] = "⚠️ La imagen " . ($index + 1) . " supera los 2 MB permitidos.";
            continue;
         }

         // Validar tipo de archivo
         $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
         if (!in_array($_FILES[$img_field]['type'], $allowed_types)) {
            $message[] = "⚠️ Formato no válido en imagen " . ($index + 1) . ".";
            continue;
         }

         // Actualizar imagen en la base
         $update_image = $conn->prepare("UPDATE `products` SET $img_field = ? WHERE id = ?");
         $update_image->execute([$new_image_name, $pid]);

         move_uploaded_file($tmp_name, $folder);

         // Eliminar imagen anterior
         $old_path = '../uploaded_img/' . $old_image;
         if (file_exists($old_path)) unlink($old_path);

         $message[] = "🖼️ Imagen " . ($index + 1) . " actualizada correctamente.";
      }
   }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Actualizar Producto | Petals by Montse</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="update-product">
   <h1 class="heading">Actualizar producto</h1>

   <?php
   $update_id = $_GET['update'] ?? null;
   if (!$update_id) {
      echo '<p class="empty">ID de producto no especificado.</p>';
   } else {
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$update_id]);

      if ($select_products->rowCount() > 0) {
         $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
   ?>

   <form action="" method="post" enctype="multipart/form-data">
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="old_image_01" value="<?= htmlspecialchars($fetch_products['image_01']); ?>">
      <input type="hidden" name="old_image_02" value="<?= htmlspecialchars($fetch_products['image_02']); ?>">
      <input type="hidden" name="old_image_03" value="<?= htmlspecialchars($fetch_products['image_03']); ?>">

      <div class="image-container">
         <div class="main-image">
            <img src="../uploaded_img/<?= htmlspecialchars($fetch_products['image_01']); ?>" alt="Imagen principal">
         </div>
         <div class="sub-image">
            <img src="../uploaded_img/<?= htmlspecialchars($fetch_products['image_01']); ?>" alt="">
            <img src="../uploaded_img/<?= htmlspecialchars($fetch_products['image_02']); ?>" alt="">
            <img src="../uploaded_img/<?= htmlspecialchars($fetch_products['image_03']); ?>" alt="">
         </div>
      </div>

      <span>Nombre del producto</span>
      <input type="text" name="name" class="box" required maxlength="100"
             value="<?= htmlspecialchars($fetch_products['name']); ?>">

      <span>Precio</span>
      <input type="number" name="price" class="box" required min="0" max="9999999"
             step="0.01" value="<?= htmlspecialchars($fetch_products['price']); ?>">

      <span>Descripción</span>
      <textarea name="details" class="box" required cols="30" rows="10"><?= htmlspecialchars($fetch_products['details']); ?></textarea>

      <span>Imagen principal</span>
      <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">

      <span>Imagen secundaria 1</span>
      <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">

      <span>Imagen secundaria 2</span>
      <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">

      <div class="flex-btn">
         <input type="submit" name="update" class="btn" value="Actualizar producto">
         <a href="products.php" class="option-btn">Volver</a>
      </div>
   </form>

   <?php
      } else {
         echo '<p class="empty">No se encontró el producto.</p>';
      }
   }
   ?>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
