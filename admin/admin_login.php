<?php
include '../components/connect.php';
session_start();

// ✅ Si el admin ya inició sesión, redirigirlo al dashboard
if (isset($_SESSION['admin_id'])) {
   header('location:dashboard.php');
   exit;
}

if (isset($_POST['submit'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']); // ⚠️ SHA1 se mantiene para compatibilidad con tu base actual

   // Buscar al administrador
   $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);

   if ($select_admin->rowCount() > 0) {
      $row = $select_admin->fetch(PDO::FETCH_ASSOC);
      $_SESSION['admin_id'] = $row['id'];
      header('location:dashboard.php');
      exit;
   } else {
      $message[] = '⚠️ Nombre de usuario o contraseña incorrectos.';
   }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Acceso Administrativo | Petals by Montse</title>

   <!-- Íconos y estilos -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php
// Mostrar mensajes
if (isset($message)) {
   foreach ($message as $msg) {
      echo '
      <div class="message">
         <span>' . htmlspecialchars($msg) . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<section class="form-container">

   <form action="" method="post" autocomplete="off">
      <h3>Panel de Administración</h3>
      <p>Inicie sesión para acceder a la gestión del sistema</p>

      <input type="text" name="name" required 
             placeholder="Nombre de usuario" 
             maxlength="20" class="box"
             oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="password" name="pass" required 
             placeholder="Contraseña" 
             maxlength="20" class="box"
             oninput="this.value = this.value.replace(/\s/g, '')">

      <input type="submit" value="Ingresar al sistema" class="btn" name="submit">
   </form>

</section>

</body>
</html>
