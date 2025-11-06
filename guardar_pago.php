<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método no permitido');
}

if (!isset($_SESSION['datos_registro'])) {
    die('No hay datos de registro en sesión');
}

$orderID = $_POST['orderID'] ?? '';
$details = json_decode($_POST['details'] ?? '', true);

if (empty($orderID) || empty($details)) {
    die('Datos de pago incompletos');
}

$_SESSION['paypal_order_id'] = $orderID;
$_SESSION['paypal_payer_id'] = $details['payer']['payer_id'] ?? '';
$_SESSION['paypal_data'] = json_encode($details);

echo 'OK';