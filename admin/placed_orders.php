<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if(!$admin_id){
   header('location:admin_login.php');
   exit;
}

// ✅ Actualizar estado del pago
if(isset($_POST['update_payment'])){
   $order_id = $_POST['order_id'];
   $payment_status = filter_var($_POST['payment_status'], FILTER_SANITIZE_STRING);

   $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_payment->execute([$payment_status, $order_id]);

   $message[] = '¡Estado del pago actualizado correctamente!';
}

// ✅ Eliminar pedido
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Gestión de Pedidos | Panel Petals by Montse</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="orders">

   <h1 class="heading">Gestión de Pedidos</h1>

   <div class="box-container">
      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders` ORDER BY id DESC");
         $select_orders->execute();

         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <p><strong>Colocado el:</strong> <span><?= htmlspecialchars($fetch_orders['placed_on']); ?></span></p>
         <p><strong>Nombre:</strong> <span><?= htmlspecialchars($fetch_orders['name']); ?></span></p>
         <p><strong>Correo:</strong> <span><?= htmlspecialchars($fetch_orders['email']); ?></span></p>
         <p><strong>Teléfono:</strong> <span><?= htmlspecialchars($fetch_orders['number']); ?></span></p>
         <p><strong>Dirección:</strong> <span><?= htmlspecialchars($fetch_orders['address']); ?></span></p>
         <p><strong>Forma de pago:</strong> <span><?= htmlspecialchars($fetch_orders['method']); ?></span></p>
         <p><strong>Productos:</strong> <span><?= htmlspecialchars($fetch_orders['total_products']); ?></span></p>
         <p><strong>Total:</strong> <span>$<?= number_format($fetch_orders['total_price'], 2); ?></span></p>

         <form action="" method="post">
            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
            <label for="status_<?= $fetch_orders['id']; ?>"><strong>Estado del pago:</strong></label>
            <select id="status_<?= $fetch_orders['id']; ?>" name="payment_status" class="select" required>
               <option value="<?= $fetch_orders['payment_status']; ?>" selected><?= ucfirst($fetch_orders['payment_status']); ?></option>
               <option value="pendiente">Pendiente</option>
               <option value="completado">Completado</option>
            </select>

            <div class="flex-btn">
               <input type="submit" value="Actualizar" class="option-btn" name="update_payment">
               <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" 
                  class="delete-btn" 
                  onclick="return confirm('¿Desea eliminar este pedido definitivamente?');">
                  Eliminar
               </a>
            </div>
         </form>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">No se han registrado pedidos aún.</p>';
         }
      ?>
   </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
