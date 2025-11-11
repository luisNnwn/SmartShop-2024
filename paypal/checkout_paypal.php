<?php
require_once __DIR__.'/paypal_config.php';
require_once __DIR__.'/../components/connect.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// 1️⃣ Validar sesión
$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) { header('Location: ../user_login.php'); exit; }

// 2️⃣ Total del pedido
$total = isset($_POST['total']) ? (float)$_POST['total'] : 0.00;
if ($total <= 0) { http_response_code(400); die('Monto inválido.'); }

// 3️⃣ Obtener token PayPal
$access = paypal_get_token($paypal_base, PAYPAL_CLIENT_ID, PAYPAL_SECRET);
if (!$access) { http_response_code(502); die('No se pudo autenticar con PayPal.'); }

// 4️⃣ Crear orden PayPal
$payload = json_encode([
    'intent' => 'CAPTURE',
    'purchase_units' => [[
        'amount' => [
            'currency_code' => 'USD',
            'value' => number_format($total, 2, '.', '')
        ],
        'custom_id' => (string)$user_id
    ]],
    'application_context' => [
        'brand_name'   => 'Petals by Montse',
        'landing_page' => 'LOGIN',
        'user_action'  => 'PAY_NOW',
        'return_url'   => 'https://'.$_SERVER['HTTP_HOST'].'/paypal/success.php',
        'cancel_url'   => 'https://'.$_SERVER['HTTP_HOST'].'/paypal/cancel.php'
    ]
]);

$ch = curl_init("$paypal_base/v2/checkout/orders");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = json_decode(curl_exec($ch), true);
if (curl_errno($ch)) error_log('[PayPal CreateOrder] '.curl_error($ch));
curl_close($ch);

// 5️⃣ Obtener link de aprobación y orderID
$approve  = null;
$order_id = $res['id'] ?? null;

if (!empty($res['links'])) {
    foreach ($res['links'] as $ln) {
        if ($ln['rel'] === 'approve') { $approve = $ln['href']; break; }
    }
}

// 6️⃣ Registrar el pre-pedido (pendiente)
if ($order_id && $user_id) {
    try {
        // Campos de sesión (si los tienes guardados)
        $name    = $_SESSION['name']    ?? 'Cliente';
        $number  = $_SESSION['phone']   ?? '';
        $email   = $_SESSION['email']   ?? '';
        $address = $_SESSION['address'] ?? '';
        $items   = 'Compra Petals by Montse';
        $total_f = number_format($total, 2, '.', '');

        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, name, number, email, method, address,
                                total_products, total_price, payment_status, paypal_order_id)
            VALUES (:uid, :name, :num, :email, 'paypal', :addr,
                    :items, :total, 'pending', :poid)
        ");
        $stmt->execute([
            ':uid'   => $user_id,
            ':name'  => $name,
            ':num'   => $number,
            ':email' => $email,
            ':addr'  => $address,
            ':items' => $items,
            ':total' => $total_f,
            ':poid'  => $order_id
        ]);
    } catch (Throwable $e) {
        error_log('[Pre-Order Save Error] '.$e->getMessage());
    }
}

// 7️⃣ Redirigir a PayPal si todo bien
if ($approve) {
    header("Location: $approve");
    exit;
}

// 8️⃣ Si falla
http_response_code(502);
echo "<h3>❌ Error al crear la orden en PayPal</h3><pre>";
print_r($res);
echo "</pre>";
?>
