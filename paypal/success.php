<?php
require_once __DIR__.'/paypal_config.php';
require_once __DIR__.'/../components/connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// PayPal redirige con ?token=<orderID>
$orderID = $_GET['token'] ?? '';
if (!$orderID) { http_response_code(400); die('Falta token de orden.'); }

$access = paypal_get_token($paypal_base, PAYPAL_CLIENT_ID, PAYPAL_SECRET);
if (!$access) { http_response_code(502); die('No se pudo autenticar con PayPal.'); }

// Capturar el pago
$ch = curl_init("$paypal_base/v2/checkout/orders/$orderID/capture");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = json_decode(curl_exec($ch), true);
if (curl_errno($ch)) error_log('[PayPal Capture] '.curl_error($ch));
curl_close($ch);

// Validar resultado
$status = $res['status'] ?? '';
if ($status === 'COMPLETED' || $status === 'APPROVED') {
    // ⚙️ Marca pedido como pagado en tu BD usando $orderID / $res['id'] / $res['purchase_units'][0]['payments']['captures'][0]['id']
    echo "<h2>✅ Pago completado</h2><pre>";
    print_r($res);
    echo "</pre>";
} else {
    http_response_code(400);
    echo "<h3>⚠️ Estado inesperado: $status</h3><pre>";
    print_r($res);
    echo "</pre>";
}
