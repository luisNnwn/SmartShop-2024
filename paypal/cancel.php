<?php
if (session_status() === PHP_SESSION_NONE) session_start();
echo "<h2>❌ Pago cancelado</h2>";
echo '<p>Regresa a tu <a href="/cart.php">carrito</a> para intentarlo de nuevo.</p>';
