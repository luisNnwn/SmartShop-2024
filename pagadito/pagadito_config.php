<?php
// =========================================================
// CONFIGURACIÓN PAGADITO - PRODUCCIÓN
// =========================================================

// Credenciales (protegidas por variables de entorno)
define('PAGADITO_UID', getenv('PAGADITO_UID') ?: '');
define('PAGADITO_WSK', getenv('PAGADITO_WSK') ?: '');

// Validar credenciales obligatorias
if (!PAGADITO_UID || !PAGADITO_WSK) {
    die('❌ Error: Credenciales Pagadito no configuradas. Verifica PAGADITO_UID y PAGADITO_WSK en Render.');
}

// Entorno (sandbox o production)
define('PAGADITO_ENV', getenv('PAGADITO_ENV') ?: 'production');

// URL base de Pagadito según el entorno
if (PAGADITO_ENV === 'production') {
    define('PAGADITO_API_URL', 'https://api.pagadi.to');
} else {
    define('PAGADITO_API_URL', 'https://sandbox.pagadi.to');
}

// Rutas de retorno (Render HTTPS)
define('PAGADITO_RETURN_URL', 'https://smartshop-2024.onrender.com/pagadito/return_pagado.php');
define('PAGADITO_CANCEL_URL', 'https://smartshop-2024.onrender.com/pagadito/cancel_pagado.php');
