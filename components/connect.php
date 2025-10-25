<?php
$db_host = 'host.docker.internal';
$db_name = 'shop_db';
$db_user = 'root';
$db_pass = 123456; 

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Conectado a MySQL";
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>
