<?php
// ---------------------------------------------
// CONEXIÓN REAL A MYSQL (RAILWAY + RENDER)
// ---------------------------------------------

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🚀 Toma las variables del entorno (Render)
$db_host = getenv('DB_HOST') ?: 'ballast.proxy.rlwy.net';
$db_port = getenv('DB_PORT') ?: 54282;
$db_name = getenv('DB_NAME') ?: 'railway';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: 'LUtKbjoCyRKNxGaNMhMWrozBRuLRmDTu';

try {
    $conn = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true // Mantiene viva la conexión
        ]
    );
    // echo "<p style='color:lime;text-align:center;'>✅ Conectado a MySQL</p>";
} catch (PDOException $e) {
    // Muestra un aviso visual si hay un problema real (no modo demo)
    echo "<p style='color:orange;text-align:center;margin-top:1rem;'>⚠️ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
    $conn = null;
}
?>

