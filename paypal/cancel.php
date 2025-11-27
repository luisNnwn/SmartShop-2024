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
.paypal-container{
   max-width:520px;
   margin:4rem auto;
   background:#fff;
   border:1px solid rgba(211,108,140,.25);
   border-radius:12px;
   padding:3rem 2rem;
   text-align:center;
   box-shadow:0 8px 20px rgba(211,108,140,.12);
   background:#fff6f8;
}

.paypal-container h2{
   font-size:2.6rem;
   color:#d0486a;
   margin-bottom:1rem;
}

.paypal-container p{
   font-size:1.6rem;
   color:#555;
   margin-bottom:1.4rem;
}

.paypal-btn{
   display:inline-block;
   padding:1.2rem 2rem;
   margin-top:1.2rem;
   background:#d0486a;
   color:#fff;
   border-radius:8px;
   font-size:1.7rem;
   transition:0.2s;
}

.paypal-btn:hover{
   background:#b33a57;
}

@media(max-width:480px){
   .paypal-container{ margin:2rem 1rem; padding:2.4rem 1.6rem; }
   .paypal-container h2{ font-size:2.3rem; }
}
</style>
</head>
<body>

<div class="paypal-container">
   <h2>❌ Pago cancelado</h2>
   <p>No se realizó ningún cargo. Puedes intentarlo nuevamente cuando gustes.</p>
   <a href="../cart.php" class="paypal-btn">Volver al carrito</a>
</div>

</body>
</html>