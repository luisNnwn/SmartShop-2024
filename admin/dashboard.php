<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
   header('location:admin_login.php');
   exit;
}

// 🔹 Traer datos del administrador logueado
$select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Panel de Control | Petals by Montse</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="dashboard">
   <h1 class="heading">Panel de Control</h1>

   <div class="box-container">

      <!-- PERFIL ADMIN -->
      <div class="box">
         <h3>Bienvenido(a)</h3>
         <p><?= htmlspecialchars($fetch_profile['name'] ?? 'Administrador'); ?></p>
         <a href="update_profile.php" class="btn">Actualizar perfil</a>
      </div>

      <!-- TOTAL PENDIENTES -->
      <div class="box">
         <?php
            $total_pendings = 0;
            $select_pendings = $conn->prepare("SELECT total_price FROM `orders` WHERE payment_status = ?");
            $select_pendings->execute(['pendiente']);
            while ($row = $select_pendings->fetch(PDO::FETCH_ASSOC)) {
               $total_pendings += $row['total_price'];
            }
         ?>
         <h3>$<?= number_format($total_pendings, 2); ?></h3>
         <p>Total pendientes</p>
         <a href="placed_orders.php" class="btn">Ver pedidos</a>
      </div>

      <!-- TOTAL COMPLETADOS -->
      <div class="box">
         <?php
            $total_completes = 0;
            $select_completes = $conn->prepare("SELECT total_price FROM `orders` WHERE payment_status = ?");
            $select_completes->execute(['completado']);
            while ($row = $select_completes->fetch(PDO::FETCH_ASSOC)) {
               $total_completes += $row['total_price'];
            }
         ?>
         <h3>$<?= number_format($total_completes, 2); ?></h3>
         <p>Pedidos completados</p>
         <a href="placed_orders.php" class="btn">Ver pedidos</a>
      </div>

      <!-- TOTAL DE PEDIDOS -->
      <div class="box">
         <?php
            $select_orders = $conn->query("SELECT COUNT(*) AS total FROM `orders`");
            $number_of_orders = $select_orders->fetch(PDO::FETCH_ASSOC)['total'];
         ?>
         <h3><?= $number_of_orders; ?></h3>
         <p>Pedidos realizados</p>
         <a href="placed_orders.php" class="btn">Ver pedidos</a>
      </div>

      <!-- TOTAL DE PRODUCTOS -->
      <div class="box">
         <?php
            $select_products = $conn->query("SELECT COUNT(*) AS total FROM `products`");
            $number_of_products = $select_products->fetch(PDO::FETCH_ASSOC)['total'];
         ?>
         <h3><?= $number_of_products; ?></h3>
         <p>Productos añadidos</p>
         <a href="products.php" class="btn">Ver productos</a>
      </div>

      <!-- TOTAL DE USUARIOS -->
      <div class="box">
         <?php
            $select_users = $conn->query("SELECT COUNT(*) AS total FROM `users`");
            $number_of_users = $select_users->fetch(PDO::FETCH_ASSOC)['total'];
         ?>
         <h3><?= $number_of_users; ?></h3>
         <p>Usuarios registrados</p>
         <a href="users_accounts.php" class="btn">Ver usuarios</a>
      </div>

      <!-- TOTAL DE ADMINISTRADORES -->
      <div class="box">
         <?php
            $select_admins = $conn->query("SELECT COUNT(*) AS total FROM `admins`");
            $number_of_admins = $select_admins->fetch(PDO::FETCH_ASSOC)['total'];
         ?>
         <h3><?= $number_of_admins; ?></h3>
         <p>Administradores</p>
         <a href="admin_accounts.php" class="btn">Ver administradores</a>
      </div>

      <!-- MENSAJES NUEVOS -->
      <div class="box">
         <?php
            $select_messages = $conn->query("SELECT COUNT(*) AS total FROM `messages`");
            $number_of_messages = $select_messages->fetch(PDO::FETCH_ASSOC)['total'];
         ?>
         <h3><?= $number_of_messages; ?></h3>
         <p>Mensajes nuevos</p>
         <a href="messages.php" class="btn">Ver mensajes</a>
      </div>

   </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
