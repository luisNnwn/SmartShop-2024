<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if(!$admin_id){
   header('location:admin_login.php');
   exit;
}

// 🔹 Obtener datos del administrador actual
$select_admin = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$select_admin->execute([$admin_id]);
$fetch_profile = $select_admin->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['submit'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

   // Actualizar nombre
   $update_name = $conn->prepare("UPDATE `admins` SET name = ? WHERE id = ?");
   $update_name->execute([$name, $admin_id]);
   $message[] = '✅ Nombre actualizado correctamente.';

   // Contraseñas
   $old_pass_input = $_POST['old_pass'];
   $new_pass_input = $_POST['new_pass'];
   $confirm_pass_input = $_POST['confirm_pass'];

   $empty_hash = sha1('');

   $old_pass = sha1($old_pass_input);
   $new_pass = sha1($new_pass_input);
   $confirm_pass = sha1($confirm_pass_input);

   if($old_pass_input != '' || $new_pass_input != '' || $confirm_pass_input != '') {

      if($old_pass == $empty_hash){
         $message[] = '⚠️ Introduzca su contraseña actual.';
      } elseif($old_pass != $fetch_profile['password']){
         $message[] = '❌ La contraseña actual no coincide.';
      } elseif($new_pass != $confirm_pass){
         $message[] = '⚠️ Las contraseñas nuevas no coinciden.';
      } else {
         $update_pass = $conn->prepare("UPDATE `admins` SET password = ? WHERE id = ?");
         $update_pass->execute([$confirm_pass, $admin_id]);
         $message[] = '🔒 Contraseña actualizada correctamente.';
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
   <title>Actualizar Perfil | Panel Admin</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Actualizar perfil</h3>

      <?php
      if(isset($message)){
         foreach($message as $msg){
            echo '<p class="error-message">'.$msg.'</p>';
         }
      }
      ?>

      <input type="text" name="name" value="<?= htmlspecialchars($fetch_profile['name']); ?>"
             required placeholder="Introduzca su nombre de usuario"
             maxlength="20" class="box"
             oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="password" name="old_pass"
             placeholder="Introduzca su contraseña actual"
             maxlength="20" class="box"
             oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="password" name="new_pass"
             placeholder="Introduzca su nueva contraseña"
             maxlength="20" class="box"
             oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="password" name="confirm_pass"
             placeholder="Confirme su nueva contraseña"
             maxlength="20" class="box"
             oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="submit" value="Actualizar perfil" class="btn" name="submit">
   </form>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
