<?php
include 'connect.php';

// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Eliminar variables de sesión
$_SESSION = [];

// Destruir la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirigir al login de admin con mensaje opcional
header('Location: ../admin/admin_login.php?logout=success');
exit;
?>
