<?php
include '../components/connect.php';
session_start();

// 🔐 Validar sesión de administrador
if (!isset($_SESSION['admin_id'])) {
   header('location:admin_login.php');
   exit;
}
$admin_id = $_SESSION['admin_id'];

// 🗑️ Eliminar administrador (con protección mínima)
if (isset($_GET['delete'])) {
   $delete_id = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);

   // Evitar que el admin actual se elimine a sí mismo
   if ($delete_id == $admin_id) {
      $message[] = '⚠️ No puedes eliminar tu propia cuenta mientras estás conectado.';
   } else {
      $delete_admin = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
      $delete_admin->execute([$delete_id]);
      $message[] = '🗑️ Cuenta administradora eliminada con éxito.';
   }

   header('location:admin_accounts.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Gestión de Administradores | Petals by Montse</title>

   <!-- Íconos y estilos -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="accounts">

   <h1 class="heading">Gestión de cuentas administradoras</h1>

   <div class="box-container">

      <!-- 🔹 Sección para añadir nuevos administradores -->
      <div class="box">
         <p>¿Deseas añadir un nuevo administrador?</p>
         <a href="register_admin.php" class="option-btn">Registrar nuevo</a>
      </div>

      <?php
      // 🧾 Mostrar cuentas de administradores
      $select_accounts = $conn->prepare("SELECT * FROM `admins` ORDER BY id ASC");
      $select_accounts->execute();

      if ($select_accounts->rowCount() > 0) {
         while ($fetch_account = $select_accounts->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="box">
         <p>ID Administrador: <span><?= htmlspecialchars($fetch_account['id']); ?></span></p>
         <p>Nombre: <span><?= htmlspecialchars($fetch_account['name']); ?></span></p>

         <div class="flex-btn">
            <?php if ($fetch_account['id'] != $admin_id): ?>
               <a href="admin_accounts.php?delete=<?= $fetch_account['id']; ?>" 
                  onclick="return confirm('¿Eliminar esta cuenta administradora?')" 
                  class="delete-btn">Eliminar</a>
            <?php else: ?>
               <a href="update_profile.php" class="option-btn">Actualizar mi perfil</a>
            <?php endif; ?>
         </div>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">🌿 No hay cuentas administradoras registradas.</p>';
      }
      ?>

   </div>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
