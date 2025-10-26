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

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Sobre Nosotros | Petals by Montse</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="about">

   <div class="row">

      <div class="image">
         <img src="images/about-img.svg" alt="Sobre Petals by Montse">
      </div>

      <div class="content">
         <h3>💐 Nuestra historia</h3>
         <p>
            En <strong>Petals by Montse</strong> creemos que cada flor cuenta una historia.  
            Nacimos con la ilusión de llevar belleza, amor y frescura a cada rincón de El Salvador  
            a través de arreglos florales únicos, creados con pasión y detalle.
         </p>
         <p>
            Nos inspira la naturaleza y el arte de regalar emociones.  
            Cada pétalo, color y aroma es cuidadosamente seleccionado para reflejar sentimientos sinceros  
            en cumpleaños, aniversarios, bodas o simplemente para decir “te pienso”.
         </p>
         <a href="contact.php" class="btn">Contáctanos</a>
      </div>

   </div>

</section>

<section class="reviews">
   
   <h1 class="heading">Reseñas de nuestros clientes</h1>

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <img src="images/pic-5.jpg" alt="Cliente feliz">
            <p>Encargué un ramo para el cumpleaños de mi madre y quedó encantada. Las flores llegaron frescas, hermosas y con un aroma espectacular. Petals by Montse es sin duda mi florería favorita.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3><a href="https://www.facebook.com/" target="_blank">Cinthia Rivera</a></h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-1.jpg" alt="Cliente satisfecho">
            <p>Los arreglos que ofrecen son espectaculares. Pedí uno para una boda y fue el centro de atención. Se nota el cuidado y amor con que trabajan cada detalle.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3><a href="https://www.facebook.com/" target="_blank">Emmanuel Escobar</a></h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-3.jpg" alt="Cliente">
            <p>Petals by Montse me ayudó a personalizar un arreglo para un aniversario. Quedó hermoso, con una combinación de rosas blancas y lilas. ¡Gracias por hacerlo tan especial!</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
            </div>
            <h3><a href="https://www.facebook.com/" target="_blank">Carlos Ventura</a></h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-7.jpg" alt="Cliente">
            <p>Excelente servicio al cliente y entrega puntual. Las flores llegaron en perfectas condiciones. Definitivamente volveré a comprar aquí.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3><a href="https://www.facebook.com/" target="_blank">Leonardo Iraheta</a></h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-2.jpg" alt="Cliente">
            <p>Hermosos arreglos, excelente atención y entrega rápida. Cada detalle demuestra profesionalismo y amor por lo que hacen. ¡Súper recomendado!</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
            </div>
            <h3><a href="https://www.facebook.com/" target="_blank">Harold Orellana</a></h3>
         </div>

      </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>

<script>
var swiper = new Swiper(".reviews-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: { slidesPerView:1 },
      768: { slidesPerView: 2 },
      991: { slidesPerView: 3 },
   },
});
</script>

</body>
</html>
