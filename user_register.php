<?php
include 'components/connect.php';

// Aseguramos la sesión
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

$user_id = $_SESSION['user_id'] ?? '';

if (isset($_POST['submit'])) {

   // Sanitización moderna y segura
   $name  = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
   $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

   $pass  = sha1(trim($_POST['pass']));
   $cpass = sha1(trim($_POST['cpass']));

   // Verificar existencia de usuario
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);

   if ($select_user->rowCount() > 0) {
      $message[] = '🌸 Este correo ya está registrado.';
   } else {
      if ($pass !== $cpass) {
         $message[] = '💐 Las contraseñas no coinciden.';
      } else {

         // Insertar usuario
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
         $insert_user->execute([$name, $email, $cpass]);

         // Mensaje estándar sin retrasos
         $message[] = '✨ Registro exitoso. Bienvenida a Petals by Montse 🌷';

         // --------------------------------------------------------------------
         // 🚫 PHPMailer está DESACTIVADO
         // En vez de enviar un correo, simulamos éxito inmediato
         // --------------------------------------------------------------------
         // $mail_enviado = true;  // Si algún día querés reactivarlo
         // --------------------------------------------------------------------
      }
   }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Registro | Petals by Montse</title>
   
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
      <h3>🌷 Crea tu cuenta</h3>

      <?php
      if (!empty($message)) {
         foreach ($message as $msg) {
            echo '<p class="error-message">'.htmlspecialchars($msg, ENT_QUOTES, 'UTF-8').'</p>';
         }
      }
      ?>

      <input type="text" name="name" required placeholder="Tu nombre completo" maxlength="40" class="box">
      <input type="email" name="email" required placeholder="Tu correo electrónico" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Crea una contraseña segura" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirma tu contraseña" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Registrarme" class="btn" name="submit">
      <p>¿Ya tienes una cuenta?</p>
      <a href="user_login.php" class="option-btn">Iniciar sesión</a>
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
