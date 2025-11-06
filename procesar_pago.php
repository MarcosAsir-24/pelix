<?php
session_start();

// Configurar cabeceras para desarrollo (quitar en producción)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: text/plain");

// Verificar que la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Método no permitido');
}

// Verificar datos de sesión
if (!isset($_SESSION['datos_registro'])) {
    http_response_code(400);
    die('No hay datos de registro en sesión');
}

// Obtener datos del pago
$orderID = $_POST['orderID'] ?? '';
$details = json_decode($_POST['details'] ?? '', true);

if (empty($orderID) || json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    die('Datos de pago incompletos o inválidos');
}

// Validar estructura básica de los datos
if (!isset($details['payer'], $details['payer']['payer_id'], $details['status'])) {
    http_response_code(400);
    die('Estructura de datos de pago inválida');
}

// Guardar información en sesión
$_SESSION['paypal_order_id'] = $orderID;
$_SESSION['paypal_payer_id'] = $details['payer']['payer_id'];
$_SESSION['paypal_data'] = json_encode($details);

// Después de procesar el pago y registrar el usuario correctamente:
if ($pago_exitoso && $usuario_id && $email_usuario) {
    // Llama a enviar_correo.js con Node.js y pasa el email como argumento
    $email = escapeshellarg($email_usuario);
    $nombre = escapeshellarg($nombre_usuario ?? '');
    $cmd = "node enviar_correo.js $email $nombre";
    exec($cmd . " > /dev/null 2>&1 &");
}

// Supón que aquí tienes $email y $nombre del usuario que acaba de pagar
require_once __DIR__ . '/enviar_correo.php';

// Llama a la función después de confirmar que el pago fue exitoso
if ($pago_exitoso) { // Cambia esto por tu condición real de éxito de pago
    enviarCorreoBienvenida($email, $nombre);
}

// Responder con éxito
echo 'OK';