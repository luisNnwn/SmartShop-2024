<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if(!$admin_id){
   header('location:admin_login.php');
   exit;
}

if(isset($_POST['submit'])){
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   // Validaciones
   if(strlen($pass) < 6){
      $message[] = 'La contraseña debe tener al menos 6 caracteres.';
   } elseif($pass !== $cpass){
      $message[] = '¡Las contraseñas no coinciden!';
   } else {
      // Comprobar si ya existe
      $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
      $select_admin->execute([$name]);

      if($select_admin->rowCount() > 0){
         $message[] = '¡El nombre de usuario ya existe!';
      } else {
         // Guardar con hash seguro
         $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);

         $insert_admin = $conn->prepare("INSERT INTO `admins`(name, password) VALUES(?, ?)");
         $insert_admin->execute([$name, $hashed_pass]);

         if($insert_admin){
            $message[] = '✅ Nuevo administrador registrado con éxito.';
         } else {
            $message[] = '❌ Ocurrió un error al registrar el administrador.';
         }
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
   <title>Registrar Administrador | Petals by Montse</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Registrar nuevo administrador</h3>

      <?php if(isset($message)) : ?>
         <?php foreach($message as $msg): ?>
            <p class="message"><?= htmlspecialchars($msg); ?></p>
         <?php endforeach; ?>
      <?php endif; ?>

      <input type="text" name="name" 
             required 
             placeholder="Ingrese nombre de usuario" 
             maxlength="20"  
             class="box" 
             oninput="this.value=this.value.replace(/\s/g,'')">

      <input type="password" name="pass" 
             required 
             placeholder="Ingrese contraseña (mínimo 6 caracteres)" 
             maxlength="50"  
             class="box" 
             oninput="this.value=this.value.replace(/\s/g,'')">

      <input type="password" name="cpass" 
             required 
             placeholder="Confirme su contraseña" 
             maxlength="50"  
             class="box" 
             oninput="this.value=this.value.replace(/\s/g,'')">

      <input type="submit" value="Registrar" class="btn" name="submit">
   </form>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
