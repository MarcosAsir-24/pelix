<?php
session_start();

// Validar los datos del formulario
$errores = [];
$datos = $_POST;

// Limpiar el email antes de validar
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Depuración opcional: guarda el email y todos los datos en sesión para revisar en completar_registro.php
$_SESSION['debug_email'] = $email;
$_SESSION['debug_post'] = $_POST;

// Validaciones básicas
if (empty($_POST['nombre'])) $errores['nombre'] = 'Nombre es obligatorio';
if (empty($_POST['apellidos'])) $errores['apellidos'] = 'Apellidos son obligatorios';
// Solo valida el email una vez y límpialo bien
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if (empty($email) || strpos($email, '@') === false) $errores['email'] = 'Email inválido';
// Añade aquí el resto de validaciones que necesites...

// Si hay errores, volver al formulario
if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos'] = $datos;
    header('Location: micuenta.php');
    exit;
}

// Guarda el hash de la contraseña
$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
$_SESSION['datos_registro'] = [
    'nombre' => $_POST['nombre'],
    'apellidos' => $_POST['apellidos'],
    'email' => $email,
    'telefono' => $_POST['telefono'],
    'fecha_nacimiento' => $_POST['fecha_nacimiento'],
    'direccion' => $_POST['direccion'],
    'password_hash' => $password_hash,
];

// Redirigir a PayPal
header('Location: paypal.php');
exit;
?>