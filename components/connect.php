<?php
// -------------------------------------------------------
// MODO DEMO / SIN BASE DE DATOS
// Permite mostrar toda la web sin conexión activa
// -------------------------------------------------------

$db_host = getenv('DB_HOST') ?: 'host.docker.internal';
$db_name = getenv('DB_NAME') ?: 'shop_db';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    // Si falla la conexión, creamos un "falso" objeto vacío
    $conn = null;
    // No mostramos errores para que la web cargue normal
}
?>

