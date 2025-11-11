<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pago Cancelado | Petals by Montse</title>
<link rel="stylesheet" href="../css/style.css">
<style>
body { text-align:center; padding:4rem; background:#fff3f6; font-family:'Poppins',sans-serif; }
h2 { color:#d0486a; font-size:2.4rem; }
p { font-size:1.2rem; color:#555; margin-bottom:2rem; }
.btn {
   background:#d0486a; color:white; padding:10px 25px; border:none;
   border-radius:6px; font-size:1rem; cursor:pointer; text-decoration:none;
   transition:0.2s ease-in-out;
}
.btn:hover { background:#b33a57; }
</style>
</head>
<body>
    <h2>❌ Pago Cancelado</h2>
    <p>Tu transacción fue cancelada. No se ha realizado ningún cargo.</p>
    <a href="../cart.php" class="btn">Volver al carrito</a>
</body>
</html>
