<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['submit'])){
   $email = trim($_POST['email']);
   $email = filter_var($email, FILTER_SANITIZE_EMAIL);

   // Verificar si el correo existe en la base
   $check_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $check_user->execute([$email]);

   if($check_user->rowCount() == 0){
      $error_message = '🌷 No encontramos ninguna cuenta asociada a este correo.';
   } else {
      // Generar contraseña temporal
      $temp_pass = generateRandomString(10);
      $hashed_pass = sha1($temp_pass);

      // Actualizar contraseña temporal
      $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE email = ?");
      $update_pass->execute([$hashed_pass, $email]);

      if($update_pass->rowCount() > 0){
         $mail = new PHPMailer(true);

         try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'smartshopsv24@gmail.com';
            $mail->Password   = 'sehp qjua zmln xibs';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Datos del remitente
            $mail->setFrom('smartshopsv24@gmail.com', 'Petals by Montse');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = '🌸 Recuperación de Contraseña - Petals by Montse';
            $mail->Body    = '
               <html>
               <head>
                  <style>
                     body { font-family: "Nunito", sans-serif; color:#444; background-color:#fff8f9; }
                     .container { padding:20px; border-radius:8px; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.1); }
                     h2 { color:#d36c8c; }
                     p { font-size:16px; line-height:1.5; }
                     .code { background:#d36c8c; color:#fff; display:inline-block; padding:10px 18px; border-radius:6px; letter-spacing:1px; font-weight:bold; }
                  </style>
               </head>
               <body>
                  <div class="container">
                     <h2>Hola 🌷</h2>
                     <p>Recibimos una solicitud para restablecer tu contraseña en <strong>Petals by Montse</strong>.</p>
                     <p>Tu nueva contraseña temporal es:</p>
                     <p class="code">'.$temp_pass.'</p>
                     <p>Por seguridad, te recomendamos iniciar sesión y cambiarla de inmediato desde tu perfil.</p>
                     <p>Gracias por confiar en nosotros,<br><strong>El equipo de Petals by Montse</strong></p>
                  </div>
               </body>
               </html>
            ';

            $mail->send();
            $success_message = '🌼 Se ha enviado una contraseña temporal a tu correo electrónico.';
         } catch (Exception $e) {
            $error_message = 'Error al enviar el correo: ' . $mail->ErrorInfo;
         }
      } else {
         $error_message = 'No se pudo actualizar la contraseña temporal.';
      }
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
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Recuperar contraseña - Petals by Montse</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Recuperar contraseña</h3>

      <?php
      if(isset($error_message)){
         echo '<p class="error-message" style="color:#d94f4f;">'.$error_message.'</p>';
      }
      if(isset($success_message)){
         echo '<p class="success-message" style="color:#4CAF50;">'.$success_message.'</p>';
      }
      ?>

      <input type="email" name="email" required placeholder="Ingresa tu correo electrónico" maxlength="50" class="box">
      <input type="submit" value="Enviar contraseña temporal" class="btn" name="submit">
   </form>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
