<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      $message[] = '¡El correo electrónico ya existe!';
   }else{
      if($pass != $cpass){
         $message[] = '¡Confirmar contraseña no coincide!';
      }else{
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, password) VALUES(?,?,?)");
         $insert_user->execute([$name, $email, $cpass]);
         $message[] = 'Registrado con éxito, ¡inicie sesión ahora por favor!';
         
         // Enviar correo de bienvenida
         $to = $email;
         $subject = 'Bienvenido a nuestro sitio';
         $message_body = 'Hola ' . $name . ',¡Bienvenido a nuestro sitio! Gracias por registrarte.';
         $headers = 'From: smartshopsv24@gmail.com' . "\r\n" .
            'Reply-To: smartshopsv24@gmail.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

         mail($to, $subject, $message_body, $headers);
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Registrarse</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Regístrese ahora.</h3>
      <?php
      if(isset($message)){
         foreach($message as $msg){
            echo '<p class="error-message">' . $msg . '</p>';
         }
      }
      ?>
      <input type="text" name="name" required placeholder="Introduzca su nombre de usuario" maxlength="20"  class="box">
      <input type="email" name="email" required placeholder="Introduzca su correo electrónico" maxlength="50"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Introduzca su contraseña" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="Confirme su contraseña" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Regístrese ahora" class="btn" name="submit">
      <p>¿Ya tiene una cuenta?</p>
      <a href="user_login.php" class="option-btn">Inicie sesión</a>
   </form>

</section>










<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>