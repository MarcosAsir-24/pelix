<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Rutas del PHPMailer
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

// Función para enviar el correo de bienvenida
function enviarCorreoBienvenida($email, $nombre) {
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'xninsuskyx@gmail.com';
        $mail->Password = 'sijb jxgu gwmw evmf';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('xninsuskyx@gmail.com', 'PELIX');
        $mail->addAddress($email, $nombre);

        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido/a a PELIX!';

        $mail->Body = '
        <div style="background:#191919;padding:32px 0 0 0;border-radius:12px;color:#fff;font-family:sans-serif;max-width:480px;margin:auto;">
            <div style="padding:32px;">
                <h2 style="color:#FFD43B;text-align:center;margin-top:0;">¡Bienvenido/a a PELIX!</h2>
                <p style="font-size:18px;">Hola <b style="color:#FFD43B;">' . htmlspecialchars($nombre) . '</b>,</p>
                <p>¡Gracias por completar tu registro y suscripción premium!<br>
                Ahora puedes disfrutar de todo nuestro catálogo de películas y series sin límites.</p>
                <p style="margin-bottom:8px;"><b>¿Qué puedes hacer ahora?</b></p>
                <ul style="color:#FFD43B;margin-top:0;">
                    <li style="margin-bottom:4px;">Ver estrenos y contenido exclusivo</li>
                    <li style="margin-bottom:4px;">Crear tu lista de favoritos</li>
                    <li>Acceder desde cualquier dispositivo</li>
                </ul>
                <p>Si tienes cualquier duda, contacta con nuestro equipo de soporte.<br>
                ¡Esperamos que disfrutes la experiencia PELIX!</p>
                <div style="text-align:center;margin:32px 0;">
                    <a href="http://localhost/TFG/pagina_principal.php" style="background:#FFD43B;color:#191919;text-decoration:none;padding:12px 32px;border-radius:8px;font-weight:bold;display:inline-block;">Ir a PELIX</a>
                </div>
            </div>
            <div style="background:#111;padding:16px 0;border-radius:0 0 12px 12px;text-align:center;color:#888;font-size:13px;">
                © ' . date('Y') . ' PELIX · Plataforma de cine y series
            </div>
        </div>
        ';

        // Texto alternativo
        $mail->AltBody = "¡Bienvenido/a a PELIX!\n\n"
            . "Hola $nombre,\n\n"
            . "¡Gracias por completar tu registro y suscripción premium!\n"
            . "Ahora puedes disfrutar de todo nuestro catálogo de películas y series sin límites.\n\n"
            . "¿Qué puedes hacer ahora?\n"
            . "- Ver estrenos y contenido exclusivo\n"
            . "- Crear tu lista de favoritos\n"
            . "- Acceder desde cualquier dispositivo\n\n"
            . "Si tienes cualquier duda, contacta con nuestro equipo de soporte.\n"
            . "¡Esperamos que disfrutes la experiencia PELIX!\n\n"
            . "Ir a PELIX: http://localhost/TFG/pagina_principal.php\n\n"
            . "© " . date('Y') . " PELIX · Plataforma de cine y series";

        $mail->send();
        return true;
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/mail_error.log', date('Y-m-d H:i:s') . ' - ' . $mail->ErrorInfo . PHP_EOL, FILE_APPEND);
        return false;
    }
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
    if ($mysqli->connect_errno) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    $correo = $_POST['correo'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    
    
    if ($correo && !$nombre) {
        $stmt = $mysqli->prepare("SELECT nombre FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->bind_result($nombre_bd);
        if ($stmt->fetch()) {
            $nombre = $nombre_bd;
        } else {
            $nombre = 'Usuario';
        }
        $stmt->close();
    } 
    elseif (!$correo) {
        echo "No se ha proporcionado un correo electrónico válido.";
        $mysqli->close();
        exit();
    }
    $mysqli->close();

    if (enviarCorreoBienvenida($correo, $nombre)) {
        echo 'Correo enviado correctamente.';
    } 
    else {
        echo 'Error al enviar el correo. Consulta mail_error.log para más detalles.';
    }
}
?>
