<?php
require_once 'pagadito_config.php';
include '../components/connect.php';
session_start();

// Aquí deberías validar con la API de Pagadito el estado real de la transacción
echo "<h2>✅ Pago recibido correctamente</h2>";
echo "<p>Gracias por tu compra. En breve te enviaremos tu comprobante.</p>";
?>
