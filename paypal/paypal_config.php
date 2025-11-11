<?php
define('PAYPAL_CLIENT_ID', getenv('PAYPAL_CLIENT_ID') ?: '');
define('PAYPAL_SECRET', getenv('PAYPAL_SECRET') ?: '');
define('PAYPAL_ENV', getenv('PAYPAL_ENV') ?: 'live'); // 'sandbox' o 'live'

if (!PAYPAL_CLIENT_ID || !PAYPAL_SECRET) {
    http_response_code(500);
    die('❌ PAYPAL: faltan credenciales (ClientID/Secret). Configúralas en Render.');
}

$paypal_base = (PAYPAL_ENV === 'live')
    ? 'https://api-m.paypal.com'
    : 'https://api-m.sandbox.paypal.com';

function paypal_get_token($base, $client, $secret) {
    $ch = curl_init("$base/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_USERPWD, "$client:$secret");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = json_decode(curl_exec($ch), true);
    if (curl_errno($ch)) error_log('[PayPal OAuth] '.curl_error($ch));
    curl_close($ch);
    return $res['access_token'] ?? null;
}
