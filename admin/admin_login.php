<?php
include '../components/connect.php';

// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

// ✅ Si el admin ya inició sesión, redirigirlo al dashboard
if (isset($_SESSION['admin_id'])) {
   header('Location: dashboard.php');
   exit;
}

if (isset($_POST['submit'])) {
   // Sanitización moderna (sin FILTER_SANITIZE_STRING)
   $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
   $pass = sha1(trim($_POST['pass'])); // ⚠️ SHA1 se mantiene para compatibilidad

   // Buscar al administrador
   $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ? AND password = ?");
   $select_admin->execute([$name, $pass]);

   if ($select_admin->rowCount() > 0) {
      $row = $select_admin->fetch(PDO::FETCH_ASSOC);
      $_SESSION['admin_id'] = $row['id'];
      header('Location: dashboard.php');
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
