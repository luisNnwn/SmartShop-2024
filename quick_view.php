<?php

include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Vista Rápida | Petals by Montse</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="quick-view">

   <h1 class="heading">Vista Rápida</h1>

   <?php
      // Sanitizar ID del producto
      $pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

      $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
      $select_products->execute([$pid]);

      if($select_products->rowCount() > 0){
         while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_product['name']); ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_product['image_01']); ?>">

      <div class="row">

         <!-- 🔹 Galería de imágenes -->
         <div class="image-container">
            <div class="main-image">
               <img src="uploaded_img/<?= htmlspecialchars($fetch_product['image_01']); ?>" alt="Imagen principal del arreglo floral">
            </div>
            <div class="sub-image">
               <?php if (!empty($fetch_product['image_01'])): ?>
                  <img src="uploaded_img/<?= htmlspecialchars($fetch_product['image_01']); ?>" alt="Vista adicional del arreglo">
               <?php endif; ?>
               <?php if (!empty($fetch_product['image_02'])): ?>
                  <img src="uploaded_img/<?= htmlspecialchars($fetch_product['image_02']); ?>" alt="Vista adicional del arreglo">
               <?php endif; ?>
               <?php if (!empty($fetch_product['image_03'])): ?>
                  <img src="uploaded_img/<?= htmlspecialchars($fetch_product['image_03']); ?>" alt="Vista adicional del arreglo">
               <?php endif; ?>
            </div>
         </div>

         <!-- 🔹 Contenido textual -->
         <div class="content">
            <div class="name"><?= htmlspecialchars($fetch_product['name']); ?></div>
            
            <div class="flex">
               <div class="price">
                  <span>$</span><?= $fetch_product['price']; ?>
               </div>
               <input type="number" name="qty" class="qty" min="1" max="99"
                      onkeypress="if(this.value.length == 2) return false;" value="1">
            </div>

            <div class="details">
               <?= nl2br(htmlspecialchars($fetch_product['details'])); ?>
            </div>

            <div class="flex-btn">
               <input type="submit" value="Añadir al carrito" class="btn" name="add_to_cart">
               <input class="option-btn" type="submit" name="add_to_wishlist" value="Añadir a favoritos">
            </div>

            <p style="margin-top:1rem; font-size:1.5rem; color:#666;">
               🌸 Cada flor es seleccionada a mano y preparada con amor por el equipo de <strong>Petals by Montse</strong>.
            </p>
         </div>

      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">🌷 El arreglo floral que buscas no está disponible actualmente.</p>';
      }
   ?>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
