<?php
require_once __DIR__.'/paypal_config.php';
require_once __DIR__.'/../components/connect.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$orderID = $_GET['token'] ?? '';
if (!$orderID) { http_response_code(400); die('❌ Falta el token de la orden.'); }

// Obtener token de acceso PayPal
$access = paypal_get_token($paypal_base, PAYPAL_CLIENT_ID, PAYPAL_SECRET);
if (!$access) { http_response_code(502); die('❌ Error autenticando con PayPal.'); }

// Capturar la orden
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

// Extraer datos clave
$status = $res['status'] ?? '';
$purchaseUnits = $res['purchase_units'][0] ?? [];
$payments = $purchaseUnits['payments']['captures'][0] ?? [];
$captureId = $payments['id'] ?? null;
$amountVal = $payments['amount']['value'] ?? 0.00;

// Validar resultado
if ($status !== 'COMPLETED' || !$captureId) {
    http_response_code(400);
    echo "<h3>⚠️ Estado inesperado: ".htmlspecialchars($status)."</h3><pre>";
    print_r($res);
    echo "</pre>";
    exit;
}

// ✅ Actualizar pedido existente
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

    // Eliminar carrito tras pago exitoso
    $del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $del->execute([$_SESSION['user_id'] ?? 0]);
} catch (Throwable $e) {
    error_log('[PayPal Success Error] '.$e->getMessage());
    http_response_code(500);
    die('Error actualizando la orden.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pago Exitoso | Petals by Montse</title>
<link rel="stylesheet" href="../css/style.css">
<style>
body { text-align:center; padding:4rem; background:#faf6f8; font-family:'Poppins',sans-serif; }
h2 { color:#c45a7b; font-size:2.4rem; }
p { font-size:1.2rem; color:#444; margin-bottom:2rem; }
.btn {
   background:#c45a7b; color:white; padding:10px 25px; border:none;
   border-radius:6px; font-size:1rem; cursor:pointer; text-decoration:none;
   transition:0.2s ease-in-out;
}
.btn:hover { background:#a74a68; }
</style>
</head>
<body>
    <h2>✅ ¡Gracias por tu compra!</h2>
    <p>Tu pago ha sido procesado correctamente. ID de transacción:</p>
    <p><strong><?= htmlspecialchars($captureId) ?></strong></p>
    <a href="../orders.php" class="btn">Ver mis pedidos</a>
</body>
</html>
