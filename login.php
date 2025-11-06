<?php
session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");

    if ($mysqli->connect_errno) {
        die("Error de conexión: " . $mysqli->connect_error);
    }
    
    if (!$mysqli->set_charset('utf8mb4')) {
        die("Error al establecer el charset: " . $mysqli->error);
    }
    
    $stmt = $mysqli->prepare("SELECT password_hash, nombre, id, suscripcion_inicio FROM usuarios WHERE email = ? LIMIT 1");
    if (!$stmt) {
        die("Error en prepare: " . $mysqli->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $hash = trim((string)$row['password_hash']);
        if (password_verify($password, $hash)) {
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['fecha_suscripcion'] = $row['suscripcion_inicio'] ?? null;
            header("Location: pagina_principal.php");
            exit();
        } 
        
        else {
            $error = "Contraseña incorrecta.";
        }
    } 
    
    else {
        $error = "Usuario no encontrado.";
    }

    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - PELIX</title>
    <style>
        body { background: #000; color: #fff; font-family: Arial; }
        .container { max-width: 400px; margin: 60px auto; background: #111; padding: 30px; border-radius: 10px; border: 1px solid #bb860b; }
        h2 { color: #bb860b; text-align: center; }
        label { display: block; margin-top: 15px; color: #bb860b; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #bb860b; background: #222; color: #fff; }
        button { margin-top: 25px; width: 100%; padding: 12px; background: #bb860b; color: #fff; border: none; border-radius: 6px; font-size: 1.1em; font-weight: bold; cursor: pointer; }
        button:hover { background: #a0760a; }
        .error { color: #bb860b; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Iniciar sesión</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Entrar</button>
        </form>
        <div style="margin-top:20px;text-align:center;">
            <a href="micuenta.php" style="color:#bb860b;">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>
</body>
</html>
