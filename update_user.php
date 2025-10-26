<?php
// ===============================================
// update_user.php — Petals by Montse
// ===============================================

include 'components/connect.php';
session_start();

// Validar sesión de usuario
if (!isset($_SESSION['user_id'])) {
   header('location:user_login.php');
   exit;
}

$user_id = $_SESSION['user_id'];

// Obtener perfil actual
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (!$fetch_profile) {
   header('location:user_login.php');
   exit;
}

// ===============================================
// Actualización de datos del usuario
// ===============================================
if (isset($_POST['submit'])) {

   $name  = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
   $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

   // Actualizar nombre y correo
   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
   $update_profile->execute([$name, $email, $user_id]);
   $message[] = '✅ Datos personales actualizados.';

   // --- Manejo de contraseñas ---
   $empty_hash = sha1('');
   $prev_pass  = $fetch_profile['password'];
   $old_pass   = sha1(trim($_POST['old_pass']));
   $new_pass   = sha1(trim($_POST['new_pass']));
   $cpass      = sha1(trim($_POST['cpass']));

   // Validaciones
   if (!empty($_POST['old_pass']) || !empty($_POST['new_pass']) || !empty($_POST['cpass'])) {

      if ($old_pass === $empty_hash) {
         $message[] = '⚠️ Ingrese su contraseña actual.';
      } elseif ($old_pass !== $prev_pass) {
         $message[] = '❌ La contraseña actual no coincide.';
      } elseif ($new_pass !== $cpass) {
         $message[] = '❌ La nueva contraseña y su confirmación no coinciden.';
      } elseif ($new_pass === $empty_hash) {
         $message[] = '⚠️ La nueva contraseña no puede estar vacía.';
      } else {
         $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_pass->execute([$cpass, $user_id]);
         $message[] = '🔐 Contraseña actualizada correctamente.';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Actualizar perfil | Petals by Montse</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .message-box {
         background: #f9ecec;
         color: #a94442;
         border-left: 4px solid #e74c3c;
         padding: 1rem;
         border-radius: .5rem;
         font-size: 1.6rem;
         text-align: center;
         margin-bottom: 1rem;
      }
   </style>
</head>

<body>
<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Actualizar perfil 🌸</h3>

      <?php
      if (!empty($message)) {
         foreach ($message as $msg) {
            echo '<p class="message-box">'.htmlspecialchars($msg, ENT_QUOTES, 'UTF-8').'</p>';
         }
      }
      ?>

      <input type="text" name="name" required
         placeholder="Ingrese su nombre completo"
         maxlength="50" class="box"
         value="<?= htmlspecialchars($fetch_profile['name'], ENT_QUOTES, 'UTF-8'); ?>">

      <input type="email" name="email" required
         placeholder="Ingrese su correo electrónico"
         maxlength="50" class="box"
         value="<?= htmlspecialchars($fetch_profile['email'], ENT_QUOTES, 'UTF-8'); ?>"
         oninput="this.value=this.value.replace(/\s/g,'')">

      <input type="password" name="old_pass"
         placeholder="Ingrese su contraseña actual"
         maxlength="20" class="box"
         oninput="this.value=this.value.replace(/\s/g,'')">

      <input type="password" name="new_pass"
         placeholder="Ingrese su nueva contraseña"
         maxlength="20" class="box"
         oninput="this.value=this.value.replace(/\s/g,'')">

      <input type="password" name="cpass"
         placeholder="Confirme su nueva contraseña"
         maxlength="20" class="box"
         oninput="this.value=this.value.replace(/\s/g,'')">

      <input type="submit" value="Actualizar datos" class="btn" name="submit">
   </form>

</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
