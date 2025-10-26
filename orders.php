<?php
include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

$user_id = $_SESSION['user_id'] ?? '';

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mis Pedidos | Petals by Montse</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="orders">

   <h1 class="heading">🌸 Mis pedidos</h1>

   <div class="box-container">
   <?php
      if ($user_id == '') {
         echo '<p class="empty">Por favor, inicia sesión para ver tus pedidos 🌷</p>';
      } else {
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? ORDER BY placed_on DESC");
         $select_orders->execute([$user_id]);

         if ($select_orders->rowCount() > 0) {
            // Variable para controlar la visualización de los botones
            $has_orders = true;

            while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
   ?>
   <div class="box">
      <p><strong>📅 Fecha del pedido:</strong> <span><?= htmlspecialchars($fetch_orders['placed_on']); ?></span></p>
      <p><strong>👤 Nombre:</strong> <span><?= htmlspecialchars($fetch_orders['name']); ?></span></p>
      <p><strong>📧 Correo electrónico:</strong> <span><?= htmlspecialchars($fetch_orders['email']); ?></span></p>
      <p><strong>📞 Número de teléfono:</strong> <span><?= htmlspecialchars($fetch_orders['number']); ?></span></p>
      <p><strong>🏡 Dirección de entrega:</strong> <span><?= htmlspecialchars($fetch_orders['address']); ?></span></p>
      <p><strong>💳 Método de pago:</strong> <span><?= htmlspecialchars($fetch_orders['method']); ?></span></p>
      <p><strong>🌼 Arreglos pedidos:</strong> <span><?= htmlspecialchars($fetch_orders['total_products']); ?></span></p>
      <p><strong>💰 Total a pagar:</strong> <span>$<?= number_format($fetch_orders['total_price'], 2); ?></span></p>
      <p><strong>📦 Estado del pago:</strong>
         <span style="color:<?= ($fetch_orders['payment_status'] == 'pendiente') ? 'red' : 'green'; ?>">
            <?= ucfirst(htmlspecialchars($fetch_orders['payment_status'])); ?>
         </span>
      </p>
   </div>
   <?php
            } // Fin del while
         } else {
            echo '<p class="empty">Aún no has realizado ningún pedido 💐</p>';
            $has_orders = false;
         }
      }
   ?>
   </div>

   <!-- 🔹 Botones de descarga solo si hay pedidos -->
   <?php if (!empty($has_orders)): ?>
   <div class="box-container" style="margin-top: 50px; display:flex; justify-content:center; gap:20px;">
      <a href="download.php?format=pdf" class="btn" style="max-width:200px; text-align:center;">Descargar PDF</a>
      <a href="download.php?format=csv" class="option-btn" style="max-width:200px; text-align:center;">Descargar CSV</a>
   </div>
   <?php endif; ?>

</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>
