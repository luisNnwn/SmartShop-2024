<?php

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

   // Verificar si el correo existe
   $check_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $check_user->execute([$email]);

   if($check_user->rowCount() == 0){
      $error_message = '🌷 No encontramos ninguna cuenta asociada a este correo.';
   } else {

      // Generar contraseña temporal
      $temp_pass = generateRandomString(10);
      $hashed_pass = sha1($temp_pass);

      // Actualizar la contraseña temporal
      $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE email = ?");
      $update_pass->execute([$hashed_pass, $email]);

      if($update_pass->rowCount() > 0){

         // PHPMailer está DESACTIVADO
         // En lugar de enviarlo por correo, mostramos la contraseña aquí
         $success_message = '
            🌼 Tu contraseña temporal es:<br><br>
            <strong style="font-size:2rem; color:#d36c8c;">'.$temp_pass.'</strong><br><br>
            👉 Inicia sesión y cámbiala en tu perfil por seguridad.
         ';

      } else {
         $error_message = '⚠️ No se pudo generar la contraseña temporal.';
      }
   }
}

function generateRandomString($length = 10) {
   $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   return substr(str_shuffle($characters), 0, $length);
}
?>
