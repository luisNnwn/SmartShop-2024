<?php
require_once '../components/connect.php';
require_once '../pagadito-sdk/Pagadito.php';
require_once 'pagadito_config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

echo "<h2>🔄 Validando pago...</h2>";

$Pagadito = new Pagadito(PAGADITO_UID, PAGADITO_WSK);
$Pagadito->change_format_json();
$Pagadito->change_currency_usd();
if (PAGADITO_ENV === 'sandbox') $Pagadito->mode_sandbox_on();

if ($Pagadito->connect()) {
    $token = $_GET['token'] ?? '';

    if ($token && $Pagadito->get_status($token)) {
        $estado = $Pagadito->get_rs_status();

        if ($estado === 'COMPLETED') {
            // ✅ Actualizar el pedido en tu base de datos
            $user_id = $_SESSION['user_id'] ?? 0;
            $update = $conn->prepare("UPDATE orders SET payment_status = 'Pagado' WHERE user_id = ?");
            $update->execute([$user_id]);

            echo "<h2>✅ Pago completado correctamente</h2>";
            echo "<p>Gracias por tu compra. Tu pago ha sido confirmado y registrado.</p>";
        } else {
            echo "<h2>⚠️ Pago no completado</h2>";
            echo "<p>Estado actual: $estado</p>";
        }
    } else {
        echo "<h2>❌ No se pudo verificar el pago</h2>";
        echo "<p>" . $Pagadito->get_rs_message() . "</p>";
    }
} else {
    echo "<h2>❌ Error de conexión con Pagadito</h2>";
    echo "<p>" . $Pagadito->get_rs_message() . "</p>";
}
?>
