<?php 
session_start();

require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51RR6KzGfkqWxMM80vCaAjvVu6pjtenqswthF6y3NMk4QeoM8GkBqs0z43AY1NyLV8FZjBmfajnJ6MCVwbgfB4LXd00D5l3kQ80');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: micuenta.php');
    exit;
}

$errores = [];
$datos = [];

// Recoger y limpiar datos
$datos['nombre'] = trim($_POST['nombre'] ?? '');
$datos['apellidos'] = trim($_POST['apellidos'] ?? '');
$datos['password'] = $_POST['password'] ?? '';
$datos['fecha_nacimiento'] = trim($_POST['fecha_nacimiento'] ?? '');
$datos['email'] = trim($_POST['email'] ?? '');
$datos['telefono'] = trim($_POST['telefono'] ?? '');
$datos['direccion'] = trim($_POST['direccion'] ?? '');
$paymentMethodId = $_POST['payment_method_id'] ?? '';

// Validaciones

if (empty($datos['nombre']) || preg_match('/\d/', $datos['nombre'])) {
    $errores['nombre'] = "El nombre no puede contener números y no puede estar vacío.";
}
if (empty($datos['apellidos']) || preg_match('/\d/', $datos['apellidos'])) {
    $errores['apellidos'] = "Los apellidos no pueden contener números y no pueden estar vacíos.";
}

if (strlen($datos['password']) < 7
    || !preg_match('/[a-zA-Z]/', $datos['password'])
    || !preg_match('/\d/', $datos['password'])
) {
    $errores['password'] = "La contraseña debe tener mínimo 7 caracteres, al menos una letra y un número.";
}

if (empty($datos['fecha_nacimiento'])) {
    $errores['fecha_nacimiento'] = "Debe ingresar una fecha de nacimiento.";
} else {
    $fechaNacimientoObj = DateTime::createFromFormat('Y-m-d', $datos['fecha_nacimiento']);
    $erroresFecha = DateTime::getLastErrors();

    if ($fechaNacimientoObj === false || $erroresFecha['warning_count'] > 0 || $erroresFecha['error_count'] > 0) {
        $errores['fecha_nacimiento'] = "La fecha debe estar en formato válido.";
    } else {
        $hoy = new DateTime();
        $edad = $hoy->diff($fechaNacimientoObj)->y;
        if ($edad < 13) {
            $errores['fecha_nacimiento'] = "Debes tener al menos 13 años para suscribirte.";
        } else {
            $datos['fecha_nacimiento'] = $fechaNacimientoObj->format('Y-m-d');
        }
    }
}

$email = $datos['email'];
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores['email'] = "El correo electrónico no es válido.";
}

if (empty($datos['telefono'])) {
    $errores['telefono'] = "El teléfono es obligatorio.";
}
if (empty($datos['direccion'])) {
    $errores['direccion'] = "La dirección es obligatoria.";
}

if (empty($paymentMethodId) || !preg_match('/^pm_\w+$/', $paymentMethodId)) {
    $errores['general'] = "No se recibió un método de pago válido.";
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos'] = $datos;
    header('Location: micuenta.php');
    exit;
}

try {
    require_once 'conexion.php';

    $check = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param("s", $datos['email']);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $_SESSION['errores'] = ['email' => 'Este correo ya está registrado.'];
        $_SESSION['datos'] = $datos;
        header('Location: micuenta.php');
        exit;
    }
    $check->close();

    $customer = \Stripe\Customer::create([
        'email' => $datos['email'],
        'name' => $datos['nombre'] . ' ' . $datos['apellidos'],
        'payment_method' => $paymentMethodId,
        'invoice_settings' => ['default_payment_method' => $paymentMethodId],
    ]);

    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => 500,
        'currency' => 'eur',
        'customer' => $customer->id,
        'payment_method' => $paymentMethodId,
        'off_session' => true,
        'confirm' => true,
        'description' => 'Suscripción mensual a PELIX',
    ]);

    $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
    $card = $paymentMethod->card;

    $stmt = $conexion->prepare("
        INSERT INTO usuarios 
        (nombre, apellidos, fecha_nacimiento, email, telefono, direccion, stripe_customer_id, tarjeta_ultimos4, tarjeta_marca, tarjeta_expiracion, password_hash)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $hash = password_hash($datos['password'], PASSWORD_DEFAULT);
    $exp = $card->exp_month . '/' . $card->exp_year;

    $stmt->bind_param(
        "sssssssssss",
        $datos['nombre'],
        $datos['apellidos'],
        $datos['fecha_nacimiento'],
        $datos['email'],
        $datos['telefono'],
        $datos['direccion'],
        $customer->id,
        $card->last4,
        $card->brand,
        $exp,
        $hash
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar los datos del usuario.");
    }

    $stmt->close();
    $conexion->close();

    // Guardar en sesión los datos de suscripción
    $_SESSION['nombre'] = $datos['nombre'];
    $_SESSION['password_hash'] = $hash;
    $_SESSION['fecha_suscripcion'] = date("Y-m-d");
    $_SESSION['success'] = "¡Te has suscrito con éxito!";

    unset($_SESSION['errores'], $_SESSION['datos']);

    // Redirigir a la página principal con sesión activa
    header("Location: index.php");
    exit;

} catch (Exception $e) {
    $_SESSION['errores'] = ['general' => "Error al procesar el pago: " . $e->getMessage()];
    $_SESSION['datos'] = $datos;
    header('Location: micuenta.php');
    exit;
}
?>
