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

if(isset($_POST['send'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $msg = filter_var($_POST['msg'], FILTER_SANITIZE_STRING);

   $select_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $select_message->execute([$name, $email, $number, $msg]);

   if($select_message->rowCount() > 0){
      $message[] = '🌸 Ya has enviado este mensaje anteriormente. ¡Gracias por contactarnos!';
   }else{
      $insert_message = $conn->prepare("INSERT INTO `messages`(user_id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$user_id, $name, $email, $number, $msg]);
      $message[] = '💌 ¡Tu mensaje ha sido enviado con éxito! En breve te contactaremos con una sonrisa.';
   }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contáctanos | Petals by Montse</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>

<body>
   
<?php include 'components/user_header.php'; ?>

<section class="contact">

   <form action="" method="post">
      <h3>🌷 Contáctanos</h3>
      <p style="font-size:1.7rem; color:#555; margin-bottom:1rem;">
         En <strong>Petals by Montse</strong> nos encantará ayudarte a elegir el detalle perfecto.  
         Cuéntanos qué tienes en mente y haremos florecer tu idea.
      </p>

      <input type="text" name="name" placeholder="Tu nombre completo" required maxlength="20" class="box">
      <input type="email" name="email" placeholder="Tu correo electrónico" required maxlength="50" class="box">
      <input type="number" name="number" min="0" max="9999999999" placeholder="Tu número de contacto" required onkeypress="if(this.value.length == 10) return false;" class="box">
      <textarea name="msg" class="box" placeholder="Cuéntanos cómo podemos ayudarte..." cols="30" rows="8" required></textarea>
      <input type="submit" value="Enviar mensaje" name="send" class="btn">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
