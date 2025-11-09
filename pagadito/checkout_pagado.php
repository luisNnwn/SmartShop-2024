<?php
require_once 'pagadito_config.php';
include '../components/connect.php';
session_start();

// Simulación: datos de pedido desde tu carrito (ajusta según tus tablas)
$user_id = $_SESSION['user_id'] ?? 0;
$total = $_POST['total'] ?? 20.00; // total de compra
$detalle = "Compra en Petals by Montse";

if (!$user_id) {
    header('Location: ../user_login.php');
    exit;
}

// Crear conexión con Pagadito
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => PAGADITO_API_URL . "/apiv3/rs/transaction",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        "uid" => PAGADITO_UID,
        "wsk" => PAGADITO_WSK,
        "amount" => $total,
        "currency" => "USD",
        "reference" => uniqid("ORDER_"),
        "description" => $detalle,
        "url" => PAGADITO_RETURN_URL,
        "cancel_url" => PAGADITO_CANCEL_URL
    ])
]);

$response = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);

//  Procesar respuesta
if ($error) {
    die("❌ Error de conexión con Pagadito: $error");
}

$data = json_decode($response, true);

if (isset($data['code']) && $data['code'] == 'PG1001') {
    // Transacción generada correctamente
    $redirect_url = $data['value']['url'];
    header("Location: $redirect_url");
    exit;
} else {
    echo "<h3>❌ Error al generar transacción:</h3><pre>";
    print_r($data);
    echo "</pre>";
}
?>
