<?php
// PRODUCCIÓN Pagadito
define('PAGADITO_UID', getenv('PAGADITO_UID') ?: '');
define('PAGADITO_WSK', getenv('PAGADITO_WSK') ?: '');

if (!PAGADITO_UID || !PAGADITO_WSK) {
    die('❌ Pagadito: faltan credenciales de producción (UID/WSK).');
}

define('PAGADITO_ENV', getenv('PAGADITO_ENV') ?: 'production'); // production

// URLs del SDK (no toques)
define('PAGADITO_RETURN_URL', 'https://smartshop-2024.onrender.com/pagadito/return_pagado.php');
define('PAGADITO_CANCEL_URL', 'https://smartshop-2024.onrender.com/pagadito/cancel_pagado.php');
