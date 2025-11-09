<?php
if (session_status() === PHP_SESSION_NONE) session_start();

echo "<h2>⚠️ Pago cancelado</h2>";
echo "<p>Has cancelado tu transacción. Puedes intentar nuevamente desde tu carrito.</p>";
?>
