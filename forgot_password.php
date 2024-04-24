<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Incluye la biblioteca PHPMailer

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   // Generar contraseña temporal
   $temp_pass = generateRandomString(10); // Función para generar una cadena aleatoria

   // Encriptar la contraseña temporal
   $hashed_pass = sha1($temp_pass);

   // Actualizar la contraseña temporal en la base de datos
   $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE email = ?");
   $update_pass->execute([$hashed_pass, $email]);

   if($update_pass->rowCount() > 0){
      // Enviar correo electrónico con la contraseña temporal utilizando PHPMailer
      $mail = new PHPMailer(true);

      try {
         // Configuración del servidor SMTP
         $mail->isSMTP();
         $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP de Gmail
         $mail->SMTPAuth   = true;
         $mail->Username   = 'smartshopsv24@gmail.com'; // Tu dirección de correo de Gmail
         $mail->Password   = 'sehp qjua zmln xibs'; // Tu contraseña de Gmail
         $mail->SMTPSecure = 'tls';
         $mail->Port       = 587;

         // Destinatario y contenido del correo
         $mail->setFrom('smartshopsv24@gmail.com', 'SmartShop');
         $mail->addAddress($email);
         $mail->isHTML(true);
         $mail->Subject = 'Contraseña temporal';
         $mail->Body    = 'Su contraseña temporal es: ' . $temp_pass;

         $mail->send();
         $success_message = 'Se ha enviado una contraseña temporal a su correo electrónico.';
      } catch (Exception $e) {
         $error_message = 'Error al enviar el correo: ' . $mail->ErrorInfo;
      }
   }else{
      $error_message = 'No se pudo recuperar la contraseña. Verifique su correo electrónico.';
   }
}

function generateRandomString($length = 10) {
   $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $randomString = '';
   for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
   }
   return $randomString;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Recuperar Contraseña</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Recuperar Contraseña</h3>
      <?php
      if(isset($error_message)){
         echo '<p class="error-message">' . $error_message . '</p>';
      }
      if(isset($success_message)){
         echo '<p class="success-message">' . $success_message . '</p>';
      }
      ?>
      <input type="email" name="email" required placeholder="Introduzca su correo electrónico" maxlength="50"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Enviar Contraseña Temporal" class="btn" name="submit">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
