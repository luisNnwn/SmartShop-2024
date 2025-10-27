<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
   header('location:admin_login.php');
   exit;
}

// 🗑️ Eliminar mensaje
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:messages.php');
   exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mensajes Recibidos | Panel Petals by Montse</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contacts">

   <h1 class="heading">Mensajes Recibidos</h1>

   <div class="box-container">

      <?php
         $select_messages = $conn->prepare("SELECT * FROM `messages` ORDER BY id DESC");
         $select_messages->execute();

         if ($select_messages->rowCount() > 0) {
            while ($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)) {
      ?>
      <div class="box">
         <p><strong>ID de usuario:</strong> <span><?= htmlspecialchars($fetch_message['user_id']); ?></span></p>
         <p><strong>Nombre:</strong> <span><?= htmlspecialchars($fetch_message['name']); ?></span></p>
         <p><strong>Correo electrónico:</strong> <span><?= htmlspecialchars($fetch_message['email']); ?></span></p>
         <p><strong>Teléfono:</strong> <span><?= htmlspecialchars($fetch_message['number']); ?></span></p>
         <p><strong>Mensaje:</strong> <span><?= nl2br(htmlspecialchars($fetch_message['message'])); ?></span></p>
         <a href="messages.php?delete=<?= $fetch_message['id']; ?>" 
            onclick="return confirm('¿Eliminar este mensaje?');" 
            class="delete-btn">
            <i class="fas fa-trash-alt"></i> Eliminar
         </a>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No se han recibido mensajes.</p>';
         }
      ?>

   </div>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
