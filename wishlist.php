<?php

include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
   exit;
}

include 'components/wishlist_cart.php';

// 🗑️ Eliminar un producto de la lista
if(isset($_POST['delete'])){
   $wishlist_id = $_POST['wishlist_id'];
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE id = ?");
   $delete_wishlist_item->execute([$wishlist_id]);
}

// 🧹 Vaciar toda la lista
if(isset($_GET['delete_all'])){
   $delete_wishlist_item = $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?");
   $delete_wishlist_item->execute([$user_id]);
   header('location:wishlist.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mis favoritos | Petals by Montse</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php include 'components/user_header.php'; ?>

<section class="products">

   <h3 class="heading">💖 Tus arreglos favoritos</h3>

   <div class="box-container">
   <?php
      $grand_total = 0;
      $select_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
      $select_wishlist->execute([$user_id]);
      if($select_wishlist->rowCount() > 0){
         while($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)){
            $grand_total += $fetch_wishlist['price'];
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_wishlist['pid']; ?>">
      <input type="hidden" name="wishlist_id" value="<?= $fetch_wishlist['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_wishlist['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_wishlist['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_wishlist['image']; ?>">

      <a href="quick_view.php?pid=<?= $fetch_wishlist['pid']; ?>" class="fas fa-eye" title="Ver detalles"></a>
      <img src="uploaded_img/<?= $fetch_wishlist['image']; ?>" alt="<?= htmlspecialchars($fetch_wishlist['name']); ?>">

      <div class="name"><?= $fetch_wishlist['name']; ?></div>
      <div class="flex">
         <div class="price">$<?= $fetch_wishlist['price']; ?></div>
         <input type="number" name="qty" class="qty" min="1" max="99" 
            onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>

      <input type="submit" value="Añadir al carrito" class="btn" name="add_to_cart">
      <input type="submit" value="Quitar de favoritos" 
         onclick="return confirm('¿Deseas eliminar este arreglo de tu lista de favoritos?');" 
         class="delete-btn" name="delete">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">Tu lista de favoritos está vacía 🌸</p>';
   }
   ?>
   </div>

   <?php if($select_wishlist->rowCount() > 0){ ?>
   <div class="wishlist-total">
      <p>Total estimado : <span>$<?= number_format($grand_total, 2); ?></span></p>
      <a href="shop.php" class="option-btn">Seguir explorando arreglos</a>
      <a href="wishlist.php?delete_all" 
         class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" 
         onclick="return confirm('¿Deseas eliminar todos los favoritos?');">
         Vaciar lista
      </a>
   </div>
   <?php } ?>

</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>

</body>
</html>
