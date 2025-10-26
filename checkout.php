<?php

include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
   exit();
}

if(isset($_POST['order'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $address = 'Casa/Piso No. '. $_POST['flat'] .', '. $_POST['street'] .', '. $_POST['city'] .', '. $_POST['state'] .', '. $_POST['country'] .' - '. $_POST['pin_code'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){

      $insert_order = $conn->prepare("INSERT INTO `orders`
         (user_id, name, number, email, method, address, total_products, total_price)
         VALUES(?,?,?,?,?,?,?,?)");
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = '🌷 ¡Tu pedido ha sido registrado con éxito! Pronto te contactaremos para coordinar la entrega.';
   }else{
      $message[] = '💐 Tu carrito está vacío, agrega tus flores favoritas antes de continuar.';
   }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Finalizar Pedido | Petals by Montse</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">

   <form action="" method="POST">

   <h3>💐 Detalles de tu pedido</h3>

      <div class="display-orders">
      <?php
         $grand_total = 0;
         $cart_items = [];

         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);

         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].')';
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
         <p><?= htmlspecialchars($fetch_cart['name']); ?> 
            <span>(<?= '$'.number_format($fetch_cart['price'],2).' x '.$fetch_cart['quantity']; ?>)</span>
         </p>
      <?php
            }
            $total_products = implode(', ', $cart_items);
         }else{
            echo '<p class="empty">Tu carrito está vacío 🌸</p>';
         }
      ?>
         <input type="hidden" name="total_products" value="<?= htmlspecialchars($total_products ?? ''); ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
         <div class="grand-total">
            Total general: <span>$<?= number_format($grand_total, 2); ?></span>
         </div>
      </div>

      <h3>📝 Información de entrega</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Nombre completo:</span>
            <input type="text" name="name" placeholder="Ej. Montserrat Pineda" class="box" maxlength="40" required>
         </div>
         <div class="inputBox">
            <span>Teléfono:</span>
            <input type="number" name="number" placeholder="Ej. 7856-4321" class="box" required onkeypress="if(this.value.length == 8) return false;">
         </div>
         <div class="inputBox">
            <span>Correo electrónico:</span>
            <input type="email" name="email" placeholder="Ej. montse@example.com" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Método de pago:</span>
            <select name="method" class="box" required>
               <option value="Pago contra entrega">Pago contra entrega</option>
               <option value="Tarjeta de crédito o débito">Tarjeta de crédito o débito</option>
               <option value="PayPal">PayPal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Casa/Piso Nº:</span>
            <input type="text" name="flat" placeholder="Ej. Casa #12" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Calle o avenida:</span>
            <input type="text" name="street" placeholder="Ej. Calle Los Pinos" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Ciudad:</span>
            <input type="text" name="city" placeholder="Ej. Santa Tecla" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Departamento:</span>
            <input type="text" name="state" placeholder="Ej. La Libertad" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>País:</span>
            <input type="text" name="country" value="El Salvador" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Código postal:</span>
            <input type="number" name="pin_code" placeholder="Ej. 1101" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($grand_total > 0)?'':'disabled'; ?>" value="🌷 Realizar pedido">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
