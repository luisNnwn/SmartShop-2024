<?php
include '../components/connect.php';
session_start();

// Verificar sesión
$admin_id = $_SESSION['admin_id'] ?? null;
if(!$admin_id){
   header('location:admin_login.php');
   exit;
}

// Eliminar cuenta de usuario
if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];

   try {
      // Iniciar transacción para garantizar consistencia
      $conn->beginTransaction();

      // Eliminar datos relacionados
      $conn->prepare("DELETE FROM `orders` WHERE user_id = ?")->execute([$delete_id]);
      $conn->prepare("DELETE FROM `messages` WHERE user_id = ?")->execute([$delete_id]);
      $conn->prepare("DELETE FROM `cart` WHERE user_id = ?")->execute([$delete_id]);
      $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?")->execute([$delete_id]);
      $conn->prepare("DELETE FROM `users` WHERE id = ?")->execute([$delete_id]);

      // Confirmar eliminación
      $conn->commit();
      $message[] = '✅ Usuario y toda su información relacionada eliminados correctamente.';

   } catch (Exception $e) {
      // Revertir cambios si algo falla
      $conn->rollBack();
      $message[] = '⚠️ Error al eliminar el usuario: '.$e->getMessage();
   }

   header('location:users_accounts.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cuentas de Usuarios</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="accounts">

   <h1 class="heading">Cuentas de usuarios</h1>

   <div class="box-container">

      <?php
         $select_accounts = $conn->prepare("SELECT * FROM `users` ORDER BY id DESC");
         $select_accounts->execute();

         if($select_accounts->rowCount() > 0){
            while($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="box">
         <p><strong>ID de usuario:</strong> <span><?= $fetch_accounts['id']; ?></span></p>
         <p><strong>Nombre:</strong> <span><?= htmlspecialchars($fetch_accounts['name']); ?></span></p>
         <p><strong>Correo electrónico:</strong> <span><?= htmlspecialchars($fetch_accounts['email']); ?></span></p>

         <a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>"
            onclick="return confirm('¿Desea eliminar esta cuenta? Toda la información relacionada será eliminada permanentemente.')"
            class="delete-btn">
            Eliminar usuario
         </a>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No hay cuentas de usuarios registradas.</p>';
         }
      ?>
   </div>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
