<?php
// ==========================
// Encabezado del Panel Admin
// Petals by Montse 🌸
// ==========================

// Mostrar mensajes globales
if (isset($message) && is_array($message)) {
   foreach ($message as $msg) {
      echo '
      <div class="message">
         <span>' . htmlspecialchars($msg) . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>';
   }
}
?>

<header class="header">

   <section class="flex">

      <!-- 🌸 LOGO PETALS ADMIN -->
      <a href="../admin/dashboard.php" class="logo">
         <span class="logo-highlight">Petals</span> <span class="logo-dark">Admin</span>
      </a>

      <!-- 🌼 MENÚ PRINCIPAL -->
      <nav class="navbar">
         <a href="../admin/dashboard.php">Inicio</a>
         <a href="../admin/products.php">Productos</a>
         <a href="../admin/placed_orders.php">Pedidos</a>
         <a href="../admin/admin_accounts.php">Administradores</a>
         <a href="../admin/users_accounts.php">Usuarios</a>
         <a href="../admin/messages.php">Mensajes</a>
      </nav>

      <!-- 🌷 ICONOS DE NAVEGACIÓN -->
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <!-- 🌺 PERFIL ADMIN -->
      <div class="profile">
         <?php
         $select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
         $select_profile->execute([$admin_id]);

         if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
            <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
            <a href="../admin/update_profile.php" class="btn">Actualizar perfil</a>

            <div class="flex-btn">
               <a href="../admin/register_admin.php" class="option-btn">Nuevo admin</a>
            </div>

            <a href="../components/admin_logout.php" class="delete-btn"
               onclick="return confirm('¿Desea cerrar sesión del panel Petals by Montse?');">
               Cerrar sesión
            </a>
         <?php } else { ?>
            <p>Inicie sesión para continuar</p>
            <div class="flex-btn">
               <a href="../admin/admin_login.php" class="option-btn">Iniciar sesión</a>
            </div>
         <?php } ?>
      </div>

   </section>

</header>
