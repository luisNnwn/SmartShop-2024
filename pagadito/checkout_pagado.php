<?php
require_once __DIR__ . '/pagadito_config.php';
require_once __DIR__ . '/../pagadito-sdk/Pagadito.php';
require_once __DIR__ . '/../components/connect.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (empty($_SESSION['user_id'])) { header('Location: ../user_login.php'); exit; }

// 1) Preparar monto y detalles desde tu carrito (ajusta a tu estructura)
$user_id = (int)$_SESSION['user_id'];
$total = isset($_POST['total']) ? (float)$_POST['total'] : 0.00;
if ($total <= 0) { die('Total inválido.'); }

// 2) Instanciar SDK
$pg = new Pagadito(PAGADITO_UID, PAGADITO_WSK);

// Producción: sandbox_mode OFF (por defecto)
// $pg->mode_sandbox_on();  // <-- NO USAR en producción

$pg->change_currency_usd(); // Tu tienda opera en USD

// 3) Conectar
if (!$pg->connect()) {
    die('❌ Pagadito: no se pudo conectar. Código: ' . $pg->get_rs_code() . ' - ' . $pg->get_rs_message());
}

// 4) Armar detalle (puedes iterar el carrito y agregar líneas)
$pg->add_detail(1, "Compra en Petals by Montse (usuario #$user_id)", $total);

// 5) Parám. personalizados (opcional, útiles en retorno)
$pg->set_custom_param('return_url', PAGADITO_RETURN_URL);
$pg->set_custom_param('cancel_url', PAGADITO_CANCEL_URL);
$pg->set_custom_param('uid_local', $user_id);

// 6) Ejecutar transacción → redirige al portal Pagadito si PG1002
$ern = 'ORDER_' . date('YmdHis') . '_' . $user_id; // referencia única
if (!$pg->exec_trans($ern)) {
    die('❌ Pagadito: no se pudo iniciar la transacción. Código: ' . $pg->get_rs_code() . ' - ' . $pg->get_rs_message());
}
// Si llega aquí algo falló; exec_trans hace header(Location)
