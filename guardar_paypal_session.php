<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$input = file_get_contents('php://input');
if (empty($input)) {
    die(json_encode(['error' => 'No se recibieron datos de PayPal']));
}

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode(['error' => 'Datos JSON inválidos']));
}

if (!isset($_SESSION['datos_registro'])) {
    die(json_encode(['error' => 'Sesión de registro no encontrada']));
}

if (!empty($data['paypal_email'])) {
    $_SESSION['datos_registro']['paypal_email'] = filter_var($data['paypal_email'], FILTER_SANITIZE_EMAIL);

    if (empty($_SESSION['datos_registro']['email'])) {
        $_SESSION['datos_registro']['email'] = $_SESSION['datos_registro']['paypal_email'];
    }
    
    error_log('Email guardado: ' . $_SESSION['datos_registro']['email']);
} 

else {
    die(json_encode(['error' => 'Email de PayPal no recibido']));
}

$_SESSION['datos_registro']['paypal_payer_id'] = $data['paypal_payer_id'] ?? null;
$_SESSION['datos_registro']['paypal_order_id'] = $data['paypal_order_id'] ?? null;

http_response_code(200);
echo json_encode(['success' => true, 'email' => $_SESSION['datos_registro']['email']]);