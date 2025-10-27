<?php
// =====================================
// user_login.php — Petals By Montse
// =====================================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'components/connect.php';

// =============================
// Iniciar sesión de usuario
// =============================
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

if (isset($_SESSION['user_id'])) {
   header('location:index.php');
   exit;
}

if (isset($_POST['submit'])) {
   $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
   $pass  = sha1(trim($_POST['pass']));

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if ($row) {
      $_SESSION['user_id'] = $row['id'];
      header('location:index.php');
      exit;
   } else {
      $message[] = '⚠️ Correo o contraseña incorrectos.';
   }
}

// =============================
// Recuperación de contraseña
// =============================
if (isset($_POST['forgot_submit'])) {
   $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);

   if ($select_user->rowCount() > 0) {

      // Generar contraseña temporal aleatoria segura
      $temp_pass = bin2hex(random_bytes(5));
      $hashed_pass = sha1($temp_pass);

      // Actualizar contraseña temporal
      $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE email = ?");
      $update_pass->execute([$hashed_pass, $email]);

      // Enviar correo de recuperación
      $mail = new PHPMailer(true);
      try {
         $mail->isSMTP();
         $mail->Host       = 'smtp.gmail.com';
         $mail->SMTPAuth   = true;
         $mail->Username   = 'smartshopsv24@gmail.com';
         $mail->Password   = 'sehp qjua zmln xibs'; // ⚠️ Usa una App Password válida
         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail->Port       = 587;

         $mail->setFrom('smartshopsv24@gmail.com', 'Petals By Montse');
         $mail->addAddress($email);
         $mail->isHTML(true);
         $mail->Subject = '🌸 Recuperación de contraseña - Petals By Montse';
         $mail->Body = "
            <html>
               <body style='font-family:Arial,sans-serif;background-color:#fffafc;padding:20px;'>
                  <div style='max-width:600px;margin:auto;background:#fff;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,0.1);padding:20px;'>
                     <h2 style='color:#b14f76;'>🌷 Restablecimiento de contraseña</h2>
                     <p>Hola, hemos generado una contraseña temporal para ti:</p>
                     <p style='font-size:18px;font-weight:bold;color:#e91e63;'>$temp_pass</p>
                     <p>Por favor, inicia sesión y cámbiala lo antes posible desde tu perfil.</p>
                     <br>
                     <p>💐 Gracias por confiar en <strong>Petals By Montse</strong></p>
                     <hr style='border:none;border-top:1px solid #eee;'>
                     <p style='color:#888;font-size:0.9rem;'>Este correo fue enviado automáticamente por Petals By Montse.</p>
                  </div>
               </body>
            </html>
         ";
         $mail->send();
         $message[] = '📧 Se envió una contraseña temporal a su correo.';
      } catch (Exception $e) {
         $message[] = "⚠️ Error al enviar el correo: {$mail->ErrorInfo}";
      }
   } else {
      $message[] = '❌ No se encontró una cuenta asociada a ese correo.';
   }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Iniciar sesión | Petals By Montse</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      .error-message {
         background: #f9ecec;
         color: #a94442;
         border-left: 4px solid #e74c3c;
         padding: 1rem;
         border-radius: .5rem;
         font-size: 1.6rem;
         text-align: center;
         margin-bottom: 1rem;
      }
   </style>
</head>

<body>
<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post">
      <h3>Iniciar sesión</h3>

      <?php
      if (!empty($message)) {
         foreach ($message as $msg) {
            echo '<p class="error-message">'.htmlspecialchars($msg, ENT_QUOTES, 'UTF-8').'</p>';
         }
      }
      ?>

      <input type="email" name="email" required placeholder="Correo electrónico" maxlength="50" class="box" oninput="this.value=this.value.replace(/\s/g,'')">
      <input type="password" name="pass" required placeholder="Contraseña" maxlength="20" class="box" oninput="this.value=this.value.replace(/\s/g,'')">
      <input type="submit" value="Iniciar sesión" class="btn" name="submit">

      <p><a href="#" onclick="document.getElementById('forgotForm').style.display='block';return false;" class="option-btn">¿Olvidó su contraseña?</a></p>
      <p>¿Aún no tiene una cuenta?</p>
      <a href="user_register.php" class="option-btn">Regístrese ahora</a>
   </form>

   <!-- Formulario oculto de recuperación -->
   <form id="forgotForm" action="" method="post" style="display:none; margin-top:2rem;">
      <h3>Recuperar contraseña</h3>
      <input type="email" name="email" required placeholder="Ingrese su correo registrado" maxlength="50" class="box">
      <input type="submit" value="Enviar contraseña temporal" class="btn" name="forgot_submit">
   </form>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
