<?php
require_once __DIR__ . '/pagadito_config.php';
require_once __DIR__ . '/../pagadito-sdk/Pagadito.php';
require_once __DIR__ . '/../components/connect.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// token_trans viene por GET (según manual)
$token_trans = $_GET['token'] ?? '';
if (!$token_trans) { die('Falta token de transacción.'); }

$pg = new Pagadito(PAGADITO_UID, PAGADITO_WSK);
$pg->change_currency_usd();

if (!$pg->connect()) {
    die('❌ Conexión Pagadito falló en retorno: ' . $pg->get_rs_message());
}

if ($pg->get_status($token_trans)) {
    // PG1003 → respuesta OK, revisa status
    $status = $pg->get_rs_status();   // e.g. "COMPLETED"
    $ref    = $pg->get_rs_reference();
    $fecha  = $pg->get_rs_date_trans();

    if ($status === 'COMPLETED') {
        // Marca orden pagada en tu BD (usa $ref/$token_trans/$user_id)
        echo "<h2>✅ Pago confirmado</h2><p>Ref: $ref • Fecha: $fecha</p>";
    } else {
        echo "<h3>Estado: $status</h3>";
    }
} else {
    die('❌ No se pudo consultar estado: ' . $pg->get_rs_message());
}
