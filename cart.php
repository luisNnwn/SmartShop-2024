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

if(isset($_POST['delete'])){
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
   $delete_cart_item->execute([$cart_id]);
   $message[] = '🌸 Producto eliminado del carrito.';
}

if(isset($_GET['delete_all'])){
   $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
   $delete_cart_item->execute([$user_id]);
   header('location:cart.php');
   exit();
}

if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = filter_var($_POST['qty'], FILTER_SANITIZE_NUMBER_INT);
   $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = '💐 Cantidad actualizada correctamente.';
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mi Carrito | Petals by Montse</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php include 'components/user_header.php'; ?>

<section class="products shopping-cart">

   <h3 class="heading">🛒 Mi Carrito Floral</h3>

   <p style="text-align:center; font-size:1.8rem; color:#555; margin-bottom:2rem;">
      Aquí están los arreglos que llenarán de color y amor tu día 💐
   </p>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);

      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
            $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
            $grand_total += $sub_total;
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
      <a href="quick_view.php?pid=<?= $fetch_cart['pid']; ?>" class="fas fa-eye" title="Ver detalles"></a>
      <img src="uploaded_img/<?= htmlspecialchars($fetch_cart['image']); ?>" alt="Arreglo floral">
      <div class="name"><?= htmlspecialchars($fetch_cart['name']); ?></div>
      <div class="flex">
         <div class="price">$<?= number_format($fetch_cart['price'], 2); ?></div>
         <input type="number" name="qty" class="qty" min="1" max="99"
                onkeypress="if(this.value.length == 2) return false;"
                value="<?= $fetch_cart['quantity']; ?>">
         <button type="submit" class="fas fa-edit" name="update_qty" title="Actualizar cantidad"></button>
      </div>
      <div class="sub-total">
         Subtotal: <span>$<?= number_format($sub_total, 2); ?></span>
      </div>
      <input type="submit" value="Eliminar arreglo" onclick="return confirm('¿Eliminar este arreglo del carrito?');"
             class="delete-btn" name="delete">
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">Tu carrito está vacío 🌷</p>';
      }
   ?>
   </div>

   <div class="cart-total">
      <p><strong>Total general:</strong> <span>$<?= number_format($grand_total, 2); ?></span></p>

      <div class="flex-btn">
         <a href="shop.php" class="option-btn">🌼 Seguir comprando</a>
         <a href="cart.php?delete_all" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>"
            onclick="return confirm('¿Vaciar todo el carrito?');">🗑 Vaciar carrito</a>
         <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">💳 Proceder al pago</a>
      </div>
   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
