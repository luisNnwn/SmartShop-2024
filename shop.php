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
   <title>Catálogo Floral | Petals by Montse</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>

<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products">

   <!-- 🔹 Cambio de título -->
   <h1 class="heading">Catálogo Floral</h1>

   <p style="text-align:center; font-size:1.8rem; color:#555; margin-bottom:2rem;">
      Descubre nuestras flores frescas, arreglos personalizados y detalles perfectos para cada ocasión.
   </p>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC"); 
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">

      <!-- 🔹 Mantener compatibilidad con lógica existente -->
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">

      <!-- 🔹 Iconos wishlist + vista rápida -->
      <button class="fas fa-heart" type="submit" name="add_to_wishlist" title="Agregar a favoritos"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye" title="Ver detalles"></a>

      <!-- 🔹 Imagen del producto floral -->
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="<?= htmlspecialchars($fetch_product['name']); ?>">

      <!-- 🔹 Nombre y precio -->
      <div class="name"><?= htmlspecialchars($fetch_product['name']); ?></div>
      <div class="flex">
         <div class="price"><span>$</span><?= $fetch_product['price']; ?></div>
         <input type="number" name="qty" class="qty" min="1" max="99"
                onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>

      <!-- 🔹 Botón -->
      <input type="submit" value="Añadir al carrito" class="btn" name="add_to_cart">
   </form>

   <?php
         }
      } else {
         echo '<p class="empty">🌸 No se han agregado arreglos florales aún. ¡Vuelve pronto! 🌸</p>';
      }
   ?>

   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
