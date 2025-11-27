<?php
require_once __DIR__.'/paypal_config.php';
require_once __DIR__.'/../components/connect.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$orderID = $_GET['token'] ?? '';
if (!$orderID) { http_response_code(400); die('❌ Falta el token de la orden.'); }

// Obtener token
$access = paypal_get_token($paypal_base, PAYPAL_CLIENT_ID, PAYPAL_SECRET);
if (!$access) { http_response_code(502); die('❌ Error autenticando con PayPal.'); }

// Capturar orden
$ch = curl_init("$paypal_base/v2/checkout/orders/$orderID/capture");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = json_decode(curl_exec($ch), true);
if (curl_errno($ch)) error_log('[PayPal Capture Error] '.curl_error($ch));
curl_close($ch);

// Extraer datos
$status = $res['status'] ?? '';
$purchaseUnits = $res['purchase_units'][0] ?? [];
$payments = $purchaseUnits['payments']['captures'][0] ?? [];
$captureId = $payments['id'] ?? null;
$amountVal = $payments['amount']['value'] ?? 0.00;

// Validar
if ($status !== 'COMPLETED' || !$captureId) {
    http_response_code(400);
    die("⚠️ Estado inesperado");
}

// Actualizar BD
try {
    $stmt = $conn->prepare("
        UPDATE orders
        SET payment_status = 'paid',
            paypal_capture_id = :cap,
            total_price = :total
        WHERE paypal_order_id = :ord
        LIMIT 1
    ");
    $stmt->execute([
        ':cap' => $captureId,
        ':total' => $amountVal,
        ':ord' => $orderID
    ]);

    // Vaciar carrito
    $del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $del->execute([$_SESSION['user_id'] ?? 0]);

} catch (Throwable $e) {
    error_log('[PayPal Success Error] '.$e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pago Exitoso | Petals by Montse</title>
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
}

.paypal-container h2{
   font-size:2.8rem;
   color:#c45a7b;
   margin-bottom:1rem;
}

.paypal-container p{
   font-size:1.6rem;
   color:#444;
   margin-bottom:1.4rem;
}

.paypal-btn{
   display:inline-block;
   padding:1.2rem 2rem;
   margin-top:1.2rem;
   background:#c45a7b;
   color:#fff;
   border-radius:8px;
   font-size:1.7rem;
   transition:0.2s;
}

.paypal-btn:hover{
   background:#a74a68;
}

@media (max-width:480px){
   .paypal-container{ margin:2rem 1rem; padding:2.4rem 1.6rem; }
   .paypal-container h2{ font-size:2.4rem; }
}
</style>
</head>
<body>

<div class="paypal-container">
   <h2>🌸 ¡Pago exitoso!</h2>
   <p>Tu transacción se completó correctamente.</p>
   <p><strong>ID de transacción:</strong></p>
   <p style="font-size:1.9rem; color:#d36c8c;"><?= htmlspecialchars($captureId) ?></p>
   <a href="../orders.php" class="paypal-btn">Ver mis pedidos</a>
</div>

</body>
</html>
