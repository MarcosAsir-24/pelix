<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function log_debug($msg) {
    file_put_contents(__DIR__ . '/registro_debug.log', date('Y-m-d H:i:s') . " - $msg\n", FILE_APPEND);
}

log_debug("Inicio del script registro.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    log_debug("Solicitud POST recibida");
    
    $mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
    if ($mysqli->connect_error) {
        log_debug("Error de conexión DB: " . $mysqli->connect_error);
        die("Error de conexión: " . $mysqli->connect_error);
    }

    if (!$mysqli->set_charset('utf8mb4')) {
        log_debug("Error al establecer charset: " . $mysqli->error);
        $mysqli->close();
        exit("Error charset");
    }

    // Recoge datos y registra en log
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);

    log_debug("Datos recibidos: Nombre=$nombre, Email=$email");

    // Verificar duplicado
    $check = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
    if (!$check) {
        log_debug("Error en prepare (check): " . $mysqli->error);
        $mysqli->close();
        exit("Error SQL (check)");
    }

    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        log_debug("Email ya registrado: $email");
        $check->close();
        $mysqli->close();
        exit("El email ya está registrado.");
    }
    $check->close();

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    if (!password_verify($password, $password_hash)) {
        log_debug("Error al verificar hash de contraseña");
        $mysqli->close();
        exit("Error de verificación de contraseña.");
    }

    $sql = "INSERT INTO usuarios (nombre, apellidos, fecha_nacimiento, email, telefono, direccion, password_hash) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        log_debug("Error en prepare (insert): " . $mysqli->error);
        $mysqli->close();
        exit("Error SQL (insert)");
    }

    $stmt->bind_param("sssssss", $nombre, $apellidos, $fecha_nacimiento, $email, $telefono, $direccion, $password_hash);

    if ($stmt->execute()) {
        log_debug("Usuario insertado correctamente en la BD");

        require_once __DIR__ . '/enviar_correo.php';
        log_debug("Llamando a enviarCorreoBienvenida()");

        if (!enviarCorreoBienvenida($email, $nombre)) {
            log_debug("Falló el envío de correo");
            echo "Error al enviar el correo de bienvenida. Consulta mail_error.log.";
            exit();
        }

        log_debug("Correo enviado correctamente. Redirigiendo a login.php");
        header("Location: login.php?registro=exitoso");
        exit();
    } else {
        log_debug("Error al ejecutar insert: " . $stmt->error);
        echo "Error en base de datos.";
    }

    $stmt->close();
    $mysqli->close();
} else {
    log_debug("Acceso no permitido, no es POST.");
    echo "Acceso no permitido.";
}
?>
