<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Sobre nosotros</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="about">

   <div class="row">

      <div class="image">
         <img src="images/about-img.svg" alt="">
      </div>

      <div class="content">
         <h3>Mensaje de los promotores:</h3>
         <p>En SmartShop, nos emociona darte la bienvenida a nuestra comunidad de amantes de la tecnología. Como nuevo usuario, estás a punto de descubrir un mundo de posibilidades electrónicas que harán tu vida más fácil, entretenida y conectada.</p>
         <p>Estamos emocionados de tener la oportunidad de ser parte de tu viaje tecnológico. Explora nuestro catálogo, disfruta de nuestras ofertas y descubre todo lo que SmartShop tiene para ofrecerte.</p>
         <a href="contact.php" class="btn">Contáctanos</a>
      </div>

   </div>

</section>

<section class="reviews">
   
   <h1 class="heading">Reseñas de los clientes.</h1>

   <div class="swiper reviews-slider">

   <div class="swiper-wrapper">

      <div class="swiper-slide slide">
         <img src="images/pic-5.jpg" alt="">
         <p>Llevo bastante tiempo utilizando sus servicios y nunca he tenido ningún problema con la calidad de sus productos. Los productos electrónicos en línea también funcionan muy bien. El único problema que tengo es que suelen entregar cuando estoy un poco ocupado, aunque he establecido una hora de entrega preferente. Todo lo demás ha ido bien.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3> <a href="https://www.facebook.com/" target="_blank">Cinthia Rivera</a></h3>
      </div>

      <div class="swiper-slide slide">
         <img src="images/pic-1.jpg" alt="">
         <p>Siempre hago un unboxing haciendo un vídeo y reclamo al instante si hay algo mal. A veces ni siquiera es necesario devolver el artículo y ellos tramitan el reembolso. SmartShop penaliza mucho a los vendedores que envían productos erróneos, por eso su plataforma mejora cada día.</p>
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
         <img src="images/pic-3.jpg" alt="">
         <p>SmartShop es grande si usted elige buenos vendedores . Una variedad de artículo requerido disponible. Los clientes pueden devolver y reembolsar el importe total en 7 días fácilmente. SmartShop está impulsando el comercio electrónico en El Salvador y ofrece una gran oportunidad para vender artículos en línea con facilidad.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3><a href="https://www.facebook.com/" target="_blank">Carlos Ventura</a></h3>
      </div>

      <div class="swiper-slide slide">
         <img src="images/pic-7.jpg" alt="">
         <p>Uso SmartShop para compras en línea desde hace casi 3 años. Excelente experiencia con ellos. Los vales de juego y el punto de recogida como entrega con 0 gastos de envío son servicios súper ahorradores.</p>
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
         <img src="images/pic-2.jpg" alt="">
         <p>Llevo dos años utilizando sus servicios y me han parecido muy fiables. Su política de devoluciones es lo que te da más confianza y tranquilidad. Si el producto no cumple tus expectativas o tiene algún defecto, puedes devolverlo en un plazo de siete días a partir de la fecha de entrega.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
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
      0: {
        slidesPerView:1,
      },
      768: {
        slidesPerView: 2,
      },
      991: {
        slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>