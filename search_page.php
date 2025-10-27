<?php
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

include 'components/wishlist_cart.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Buscar arreglos - Petals by Montse</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- 🔍 Sección de búsqueda -->
<section class="search-form">
   <form action="" method="post">
      <input 
         type="text" 
         name="search_box" 
         placeholder="Busca tu arreglo floral o regalo especial..." 
         maxlength="100" 
         class="box" 
         required
      >
      <button type="submit" class="fas fa-search" name="search_btn" title="Buscar"></button>
   </form>
</section>

<!-- 🌺 Resultados -->
<section class="products" style="padding-top: 0; min-height:100vh;">
   <h1 class="heading">Resultados de tu búsqueda</h1>

   <div class="box-container">
   <?php
   if(isset($_POST['search_box']) || isset($_POST['search_btn'])){
      $search_box = trim($_POST['search_box']);
      $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ?");
      $select_products->execute(["%$search_box%"]);

      if($select_products->rowCount() > 0){
         while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">

      <button class="fas fa-heart" type="submit" name="add_to_wishlist" title="Agregar a favoritos"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye" title="Vista rápida"></a>
      
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="Imagen del arreglo floral">
      <div class="name"><?= htmlspecialchars($fetch_product['name']); ?></div>
      <div class="flex">
         <div class="price"><span>$</span><?= number_format($fetch_product['price'], 2); ?></div>
         <input type="number" name="qty" class="qty" min="1" max="99" 
                onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="Añadir al carrito" class="btn" name="add_to_cart">
   </form>
   <?php
         }
      } else {
         echo '<p class="empty">🌷 No se han encontrado productos con ese nombre. ¡Prueba con otra palabra clave!</p>';
      }
   } else {
      echo '<p class="empty">💐 Usa la barra superior para buscar tu arreglo favorito.</p>';
   }
   ?>
   </div>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>
