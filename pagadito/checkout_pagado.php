<?php
require_once '../components/connect.php';
require_once '../pagadito-sdk/Pagadito.php';
require_once 'pagadito_config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../user_login.php');
    exit;
}

$total = floatval($_POST['total'] ?? 0.00);
$detalle = "Compra en Petals by Montse";
$reference = uniqid("ORDER_");

// 1️⃣ Inicializar SDK
$Pagadito = new Pagadito(PAGADITO_UID, PAGADITO_WSK);
$Pagadito->change_format_json();
$Pagadito->change_currency_usd();
if (PAGADITO_ENV === 'sandbox') $Pagadito->mode_sandbox_on();

// 2️⃣ Conectar
if (!$Pagadito->connect()) {
    die("❌ Error al conectar con Pagadito: " . $Pagadito->get_rs_message());
}

// 3️⃣ Agregar detalle
$Pagadito->add_detail(1, $detalle, $total);

// 4️⃣ Ejecutar transacción (redirige automáticamente)
if (!$Pagadito->exec_trans($reference)) {
    die("❌ Error al ejecutar transacción: " . $Pagadito->get_rs_message());
}
?>
