<?php
// =========================================================
// CONFIGURACIÓN PAGADITO - MODO SANDBOX
// =========================================================

// UID y WSK de tu cuenta sandbox (por ahora usa placeholders)
define('PAGADITO_UID', getenv('PAGADITO_UID') ?: 'TU_UID_SANDBOX');
define('PAGADITO_WSK', getenv('PAGADITO_WSK') ?: 'TU_WSK_SANDBOX');

// Entorno (sandbox o production)
define('PAGADITO_ENV', getenv('PAGADITO_ENV') ?: 'sandbox');

// URL base de Pagadito según el entorno
if (PAGADITO_ENV === 'production') {
    define('PAGADITO_API_URL', 'https://api.pagadi.to');
} else {
    define('PAGADITO_API_URL', 'https://sandbox.pagadi.to');
}

// Ruta de retorno y cancelación
define('PAGADITO_RETURN_URL', 'https://smartshop-2024.onrender.com/pagadito/return_pagado.php');
define('PAGADITO_CANCEL_URL', 'https://smartshop-2024.onrender.com/pagadito/cancel_pagado.php');
?>
