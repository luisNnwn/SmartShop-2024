<?php
// ================================
// index.php - Petals by Montse
// ================================

// Conexión a la base de datos
include 'components/connect.php';

// Iniciar sesión solo si no hay una activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Capturar el ID de usuario si está logueado
$user_id = $_SESSION['user_id'] ?? '';

// Incluir lógica de wishlist y carrito
include 'components/wishlist_cart.php';

// Contar número total de productos para el slider inteligente
$count_products_stmt = $conn->prepare("SELECT COUNT(*) FROM `products`");
$count_products_stmt->execute();
$total_products = $count_products_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>Petals by Montse</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <link rel="icon" href="images/favicon.png" type="image/png">
</head>

<body>
   
<?php include 'components/user_header.php'; ?>

<!-- ===========================
     SECCIÓN INICIO / BANNER
=========================== -->
<div class="home-bg">
<section class="home">
   <div class="swiper home-slider">
      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <div class="image">
               <img src="images/home-img-1.png" alt="Ramo de rosas rojas">
            </div>
            <div class="content">
               <span>Flores frescas cada día</span>
               <h3>Ramos de Rosas Rojas</h3>
               <a href="category.php?category=rosas" class="btn">Haz tu pedido</a>
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="image">
               <img src="images/home-img-2.png" alt="Tulipanes color pastel">
            </div>
            <div class="content">
               <span>Detalles únicos para cada ocasión</span>
               <h3>Arreglos de Tulipanes</h3>
               <a href="category.php?category=tulipanes" class="btn">Ver colección</a>
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="image">
               <img src="images/home-img-3.png" alt="Caja floral premium">
            </div>
            <div class="content">
               <span>Envía amor en cada flor</span>
               <h3>Cajas Florales Premium</h3>
               <a href="shop.php" class="btn">Descubre más</a>
            </div>
         </div>

      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>
</div>

<!-- ===========================
     SECCIÓN CATEGORÍAS
=========================== -->
<section class="category">

   <h1 class="heading">Compra por ocasión</h1>

   <div class="swiper category-slider">
      <div class="swiper-wrapper">

         <a href="category.php?category=rosas" class="swiper-slide slide">
            <img src="images/icon-1.png" alt="Rosas">
            <h3>Rosas</h3>
         </a>

         <a href="category.php?category=tulipanes" class="swiper-slide slide">
            <img src="images/icon-2.png" alt="Tulipanes">
            <h3>Tulipanes</h3>
         </a>

         <a href="category.php?category=suculentas" class="swiper-slide slide">
            <img src="images/icon-3.png" alt="Suculentas">
            <h3>Suculentas</h3>
         </a>

         <a href="category.php?category=regalos" class="swiper-slide slide">
            <img src="images/icon-4.png" alt="Regalos">
            <h3>Regalos</h3>
         </a>

         <a href="category.php?category=bodas" class="swiper-slide slide">
            <img src="images/icon-7.png" alt="Bodas">
            <h3>Bodas</h3>
         </a>

         <a href="category.php?category=funerales" class="swiper-slide slide">
            <img src="images/icon-8.png" alt="Condolencias">
            <h3>Condolencias</h3>
         </a>

      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<!-- ===========================
     SECCIÓN PRODUCTOS
=========================== -->
<section class="home-products">

   <h1 class="heading">Nuevos arreglos florales</h1>

   <div class="swiper products-slider">
      <div class="swiper-wrapper">

      <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
         $select_products->execute();

         if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <form action="" method="post" class="swiper-slide slide">
         <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
         <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
         <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">

         <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
         <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
         <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">

         <div class="name"><?= $fetch_product['name']; ?></div>

         <div class="flex">
            <div class="price"><span>$</span><?= $fetch_product['price']; ?><span> USD</span></div>
            <input type="number" name="qty" class="qty" min="1" max="99"
             onkeypress="if(this.value.length == 2) return false;" value="1">
         </div>

         <input type="submit" value="Añadir al carrito" class="btn" name="add_to_cart">
      </form>
      <?php
            }
         } else {
            echo '<p class="empty">Aún no se han añadido arreglos florales.</p>';
         }
      ?>

      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<!-- Scripts -->
<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>

<script>
   // HOME SLIDER
   new Swiper(".home-slider", {
      loop: true,
      spaceBetween: 20,
      pagination: { el: ".swiper-pagination", clickable: true },
   });

   // CATEGORY SLIDER
   new Swiper(".category-slider", {
      loop: true,
      spaceBetween: 20,
      pagination: { el: ".swiper-pagination", clickable: true },
      breakpoints: {
         0: { slidesPerView: 2 },
         650: { slidesPerView: 3 },
         768: { slidesPerView: 4 },
         1024: { slidesPerView: 5 },
      },
   });

   // PRODUCTS SLIDER INTELIGENTE
   new Swiper(".products-slider", {
      loop: <?= ($total_products >= 3 ? 'true' : 'false'); ?>,
      spaceBetween: 20,
      pagination: { el: ".swiper-pagination", clickable: true },
      breakpoints: {
         550: { slidesPerView: 2 },
         768: { slidesPerView: 2 },
         1024: { slidesPerView: 3 },
      },
   });
</script>

</body>
</html>
