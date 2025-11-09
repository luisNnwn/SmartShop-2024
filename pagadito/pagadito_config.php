<?php
// =========================================================
// CONFIGURACIÓN PAGADITO - PRODUCCIÓN
// =========================================================

// Credenciales (protegidas por variables de entorno)
define('PAGADITO_UID', getenv('PAGADITO_UID') ?: '');
define('PAGADITO_WSK', getenv('PAGADITO_WSK') ?: '');

if (!PAGADITO_UID || !PAGADITO_WSK) {
    die('❌ Error: Credenciales Pagadito no configuradas. Verifica PAGADITO_UID y PAGADITO_WSK en Render.');
}

// Entorno (sandbox o production)
define('PAGADITO_ENV', getenv('PAGADITO_ENV') ?: 'sandbox');

// URL base del SDK (no usar pagadi.to)
if (PAGADITO_ENV === 'production') {
    define('PAGADITO_API_URL', 'https://comercios.pagadito.com/apipg/charges.php');
} else {
    define('PAGADITO_API_URL', 'https://sandbox.pagadito.com/comercios/apipg/charges.php');
}

// Rutas de retorno (Render HTTPS)
define('PAGADITO_RETURN_URL', 'https://smartshop-2024.onrender.com/pagadito/return_pagado.php');
define('PAGADITO_CANCEL_URL', 'https://smartshop-2024.onrender.com/pagadito/cancel_pagado.php');
