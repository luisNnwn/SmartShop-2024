<?php
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
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
   $select_user->execute([$email, $pass]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $_SESSION['user_id'] = $row['id'];
      header('location:index.php');
   }else{
      $message[] = '¡Nombre de usuario o contraseña incorrectos!';
   }

}

if(isset($_POST['forgot_submit'])){
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
      // Enviar correo electrónico con la contraseña temporal
      $to = $email;
      $subject = 'Contraseña temporal';
      $message = 'Su contraseña temporal es: ' . $temp_pass;
      $headers = 'From: smartshopsv24@gmail.com' . "\r\n" .
          'Reply-To: smartshopsv24@gmail.com' . "\r\n" .
          'X-Mailer: PHP/' . phpversion();

      mail($to, $subject, $message, $headers);

      $success_message = 'Se ha enviado una contraseña temporal a su correo electrónico.';
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
   <title>Iniciar sesión</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Iniciar sesión</h3>
      <?php
      if(isset($message)){
         echo '<p class="error-message">' . $message[0] . '</p>';
      }
      ?>
      <input type="email" name="email" required placeholder="Introduzca su correo electrónico" maxlength="50"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Introduzca su contraseña" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Iniciar sesión" class="btn" name="submit">
      <p><a href="forgot_password.php" class="option-btn">¿Olvidaste tu contraseña?</a></p>
      <p>¿No tiene una cuenta?</p>
      <a href="user_register.php" class="option-btn">Regístrese ahora.</a>
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
