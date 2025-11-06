<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['paypal_success']) && isset($_SESSION['datos_registro'])) {
    $datos = $_SESSION['datos_registro'];

    $mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
    if ($mysqli->connect_errno) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    $nombre = $mysqli->real_escape_string($datos['nombre']);
    $apellidos = $mysqli->real_escape_string($datos['apellidos']);
    $email = $mysqli->real_escape_string($datos['email']);
    $telefono = $mysqli->real_escape_string($datos['telefono']);
    $fecha_nacimiento = $mysqli->real_escape_string($datos['fecha_nacimiento']);
    $direccion = $mysqli->real_escape_string($datos['direccion']);
    $password_hash = $mysqli->real_escape_string($datos['password_hash']);
    $paypal_payer_id = isset($datos['paypal_payer_id']) ? $mysqli->real_escape_string($datos['paypal_payer_id']) : null;
    $paypal_order_id = isset($datos['paypal_order_id']) ? $mysqli->real_escape_string($datos['paypal_order_id']) : null;
    $paypal_email = isset($datos['paypal_email']) ? $mysqli->real_escape_string($datos['paypal_email']) : null;

    // Validación del email (Que no esté vacío y tenga un @)
    if (empty($datos['email']) || strpos($datos['email'], '@') === false) {
        echo "<pre>No se ha proporcionado un correo electrónico válido.</pre>";
        exit();
    }

    // Validar que el hash existe
    if (empty($datos['password_hash'])) {
        echo "<pre>Error: No se ha recibido la contraseña. Por favor, vuelve a registrarte.</pre>";
        exit();
    }

    // Comprobar si el usuario ya existia
    $check = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $check->close();
        unset($_SESSION['datos_registro']);
        echo "<pre>Este correo ya está registrado. <a href='micuenta.php'>Volver</a></pre>";
        exit();
    }
    $check->close();

    // Insertar usuario
    $query = "INSERT INTO usuarios 
        (nombre, apellidos, fecha_nacimiento, email, telefono, direccion, password_hash, premium, fecha_registro, paypal_payer_id, paypal_order_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param(
        "sssssssss",
        $nombre,
        $apellidos,
        $fecha_nacimiento,
        $email,
        $telefono,
        $direccion,
        $password_hash,
        $paypal_payer_id,
        $paypal_order_id
    );
    if (!$stmt->execute()) {
        echo "<pre>Error al registrar el usuario: " . $stmt->error . "</pre>";
        $stmt->close();
        $mysqli->close();
        exit();
    }

    $stmt->close();
    $mysqli->close();
    require_once __DIR__ . '/enviar_correo.php';
    if (!enviarCorreoBienvenida($email, $nombre)) {
        error_log("Error al enviar correo de bienvenida a $email");
    }

    // Mensaje de bienvenida
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Registro Completado - PELIX</title>
        <style>
            body { background: #000; color: #fff; font-family: Arial; }
            .container { max-width: 500px; margin: 60px auto; background: #111; padding: 40px; border-radius: 10px; border: 1px solid #bb860b; text-align: center; }
            h2 { color: #bb860b; }
            a { color: #bb860b; text-decoration: underline; }
            .bienvenida { color: #bb860b; font-size: 1.2em; margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>¡Registro y pago completados!</h2>
            <div class="bienvenida">
                ¡Bienvenido/a a <strong>PELIX</strong>, <?= htmlspecialchars($datos['nombre']) ?>!<br>
                Tu suscripción premium está activa.<br>
                Gracias por confiar en nosotros.
            </div>
            <a href="pagina_principal.php">Ir a la página principal</a>
        </div>
    </body>
    </html>
    <?php

    unset($_SESSION['datos_registro']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores = [];
    $datos = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'apellidos' => trim($_POST['apellidos'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'fecha_nacimiento' => trim($_POST['fecha_nacimiento'] ?? ''),
        'direccion' => trim($_POST['direccion'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? ''
    ];

    // Validaciones
    if (empty($datos['nombre'])) {
        $errores['nombre'] = 'El nombre es obligatorio';
    }

    if (empty($datos['apellidos'])) {
        $errores['apellidos'] = 'Los apellidos son obligatorios';
    }

    if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'Por favor ingrese un email válido';
    }

    if (empty($datos['password'])) {
        $errores['password'] = 'La contraseña es obligatoria';
    } elseif (strlen($datos['password']) < 8) {
        $errores['password'] = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($datos['password'] !== $datos['confirm_password']) {
        $errores['confirm_password'] = 'Las contraseñas no coinciden';
    }

    if (!empty($datos['fecha_nacimiento'])) {
        $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha_nacimiento']);
        if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha_nacimiento']) {
            $errores['fecha_nacimiento'] = 'Formato de fecha inválido (AAAA-MM-DD)';
        }
    }

    // Si hay errores volver al formulario
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos'] = $datos;
        header('Location: micuenta.php');
        exit();
    }
    $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);

    // Guardar datos en sesión temporal
    $_SESSION['datos_registro'] = [
        'nombre' => $datos['nombre'],
        'apellidos' => $datos['apellidos'],
        'email' => $datos['email'],
        'telefono' => $datos['telefono'],
        'fecha_nacimiento' => $datos['fecha_nacimiento'],
        'direccion' => $datos['direccion'],
        'password_hash' => $password_hash // Guardamos solo el hash por seguridad
    ];

    //Verificar datos antes de redirigir a PayPal
    error_log('Datos guardados en sesión: ' . print_r($_SESSION['datos_registro'], true));
    header('Location: paypal.php');
    exit();
}

// Si no es ninguno de los casos, redirige
header('Location: micuenta.php?error=metodo_no_permitido');
exit();
?>