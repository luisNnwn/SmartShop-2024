<?php
// ================================
// 🌸 HEADER GLOBAL - PETALS BY MONTSE
// ================================

// Evita warnings si no existe la variable $user_id
if (!isset($user_id)) {
   $user_id = '';
}

// Mostrar mensajes flash si existen
if (isset($message) && is_array($message)) {
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

<header class="header">

   <section class="flex">

      <!-- 🌸 LOGO de Petals by Montse -->
      <a href="index.php" class="logo" style="display:flex; align-items:center; gap:10px;">
         <img src="images/logo_petals.png" alt="Petals by Montse" style="height:50px;">
         <span style="font-family:'Playfair Display', serif; font-size:2.4rem; color:var(--main-color);">
            Petals <span style="color:var(--black);">by Montse</span>
         </span>
      </a>

      <!-- 🌷 MENÚ DE NAVEGACIÓN -->
      <nav class="navbar">
         <a href="index.php">Inicio</a>
         <a href="about.php">Sobre Nosotros</a>
         <a href="shop.php">Catálogo Floral</a>
         <a href="orders.php">Haz tu Pedido</a>
         <a href="contact.php">Contáctanos</a>
      </nav>

      <!-- 🛍️ ICONOS SUPERIORES -->
      <div class="icons">
         <?php
         // Si hay conexión activa, inicializamos contadores en 0
         $total_wishlist_counts = 0;
         $total_cart_counts = 0;

         // Solo ejecutar si el usuario ha iniciado sesión
         if ($user_id !== '') {
            $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
            $count_wishlist_items->execute([$user_id]);
            $total_wishlist_counts = $count_wishlist_items->rowCount();

            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_counts = $count_cart_items->rowCount();
         }
         ?>

         <div id="menu-btn" class="fas fa-bars"></div>
         <a href="search_page.php" title="Buscar productos"><i class="fas fa-search"></i></a>
         <a href="wishlist.php" title="Favoritos"><i class="fas fa-heart"></i><span>(<?= $total_wishlist_counts; ?>)</span></a>
         <a href="cart.php" title="Carrito"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_counts; ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <!-- 👤 PERFIL DE USUARIO -->
      <div class="profile">
         <?php
         if ($user_id !== '') {
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
               <p><?= htmlspecialchars($fetch_profile["name"]); ?></p>
               <a href="update_user.php" class="btn">Actualizar Perfil</a>
               <a href="components/user_logout.php" class="delete-btn" onclick="return confirm('¿Cerrar sesión en Petals by Montse?');">Cerrar Sesión</a>
            <?php
            } else {
            ?>
               <p>Inicie sesión o regístrese para hacer un pedido</p>
               <div class="flex-btn">
                  <a href="user_register.php" class="option-btn">Registrarse</a>
                  <a href="user_login.php" class="option-btn">Iniciar Sesión</a>
               </div>
            <?php
            }
         } else {
            ?>
            <p>Inicie sesión o regístrese para hacer un pedido</p>
            <div class="flex-btn">
               <a href="user_register.php" class="option-btn">Registrarse</a>
               <a href="user_login.php" class="option-btn">Iniciar Sesión</a>
            </div>
         <?php
         }
         ?>
      </div>

   </section>

</header>
