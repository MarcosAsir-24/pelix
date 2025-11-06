<?php 
session_start();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos'] ?? [];

unset($_SESSION['errores'], $_SESSION['datos']);

?>
<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - PELIX</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 10vh;
            box-sizing: border-box;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 24px;
            width: 129%;
        }
        
        .logo h1 {
            color: #bb860b;
            font-size: 3rem;
            margin: 0;
        }
        
        .register-container {
            width: 520px;
            max-width: 96%;
            margin: 0 auto;
            padding: 28px;
            background-color: #111;
            border-radius: 12px;
            box-shadow: 0 6px 24px rgba(187, 134, 11, 0.14);
            border: 1px solid #bb860b;
            box-sizing: border-box;
            font-size: 15px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #bb860b;
        }

        .register-header h2 {
            color: #bb860b;
            font-size: 1.5rem;
            margin-bottom: 6px;
        }

        .register-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            color: #bb860b;
            margin-bottom: 6px;
            font-weight: 700;
            font-size: 1rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 12px;
            background-color: #000;
            border: 1.5px solid #bb860b;
            border-radius: 8px;
            color: #bb860b;
            font-size: 1rem;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #d4a017;
            box-shadow: 0 0 0 3px rgba(187, 134, 11, 0.08);
        }

        .error-msg {
            color: #ffcc80;
            font-size: 0.95rem;
            margin-top: 4px;
            display: block;
        }

        .register-actions {
            grid-column: span 2;
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-direction: column;
        }

        .btn {
            padding: 12px 14px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
            border: none;
            width: 100%;
        }

        .btn-primary {
            background-color: #bb860b;
            color: #000;
        }

        .btn-primary:hover {
            background-color: #d4a017;
        }

        @media (max-width: 980px) {
            .register-form {
                grid-template-columns: 1fr;
            }
            .register-container {
                width: 92%;
                padding: 20px;
                font-size: 14px;
            }
            .logo h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="logo">
            <h1>PELIX</h1>
        </div>
        <div class="register-container" style="width:520px; max-width:96%; padding:28px; margin:6px auto; box-sizing:border-box; font-size:15px;">
            <div class="register-header">
                <h2>Registro de Usuario</h2>
            </div>
            <?php if (!empty($errores['general'])): ?>
                <div style="color: #bb860b; background: #330000; padding: 12px; border: 1px solid #bb860b; margin-bottom: 20px; text-align: center;">
                    <?= htmlspecialchars($errores['general']) ?>
                </div>
            <?php endif; ?>
            <form id="form-registro" method="POST" action="procesar_registro.php" novalidate class="register-form">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required 
                           value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>">
                    <span id="error-nombre" class="error-msg"><?= htmlspecialchars($errores['nombre'] ?? '') ?></span>
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" required 
                           value="<?= htmlspecialchars($datos['apellidos'] ?? '') ?>">
                    <span id="error-apellidos" class="error-msg"><?= htmlspecialchars($errores['apellidos'] ?? '') ?></span>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required 
                           value="<?= htmlspecialchars(trim($datos['email'] ?? '')) ?>" autocomplete="email">
                    <span id="error-email" class="error-msg"><?= htmlspecialchars($errores['email'] ?? '') ?></span>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" required 
                           value="<?= htmlspecialchars($datos['telefono'] ?? '') ?>">
                    <span id="error-telefono" class="error-msg"><?= htmlspecialchars($errores['telefono'] ?? '') ?></span>
                </div>
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required 
                           value="<?= htmlspecialchars($datos['fecha_nacimiento'] ?? '') ?>">
                    <span id="error-fecha_nacimiento" class="error-msg"><?= htmlspecialchars($errores['fecha_nacimiento'] ?? '') ?></span>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" id="direccion" required 
                           value="<?= htmlspecialchars($datos['direccion'] ?? '') ?>">
                    <span id="error-direccion" class="error-msg"><?= htmlspecialchars($errores['direccion'] ?? '') ?></span>
                </div>
                <div class="form-group full-width">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" required>
                    <span id="error-password" class="error-msg"><?= htmlspecialchars($errores['password'] ?? '') ?></span>
                </div>
                <div class="form-group full-width">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
                <div class="register-actions">
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </div>
                <div class="register-actions">
                    <button type="button" onclick="window.location.href='pagina_principal.php'" class="btn btn-primary">Volver a la pagina principal</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    // Validar el formulario
    document.getElementById('form-registro').addEventListener('submit', function(e) {
        const campos = ['nombre', 'apellidos', 'email', 'telefono', 'fecha_nacimiento', 'direccion', 'password'];
        let errores = {};
 
        campos.forEach(campo => {
            document.getElementById('error-' + campo).textContent = '';
        });
        
        const nombre = document.getElementById('nombre').value.trim();
        const apellidos = document.getElementById('apellidos').value.trim();
        const email = document.getElementById('email').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        const fecha_nacimiento = document.getElementById('fecha_nacimiento').value.trim();
        const direccion = document.getElementById('direccion').value.trim();
        const password = document.getElementById('password').value.trim();
        const confirm_password = document.getElementById('confirm_password').value.trim();
        
        if (nombre === '') errores.nombre = 'Este campo es obligatorio.';
        else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) errores.nombre = 'No debe contener números.';
        
        if (apellidos === '') errores.apellidos = 'Este campo es obligatorio.';
        else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellidos)) errores.apellidos = 'No debe contener números.';
        
        if (email === '') errores.email = 'Este campo es obligatorio.';
        else if (!/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/.test(email)) errores.email = 'Correo inválido.';
        
        if (telefono === '') errores.telefono = 'Este campo es obligatorio.';
        else if (!/^\d+$/.test(telefono)) errores.telefono = 'Solo debe contener números.';
        
        if (fecha_nacimiento === '') {
            errores.fecha_nacimiento = 'Este campo es obligatorio.';
        } 
        
        else {
            const hoy = new Date();
            const fecha = new Date(fecha_nacimiento);
            let edad = hoy.getFullYear() - fecha.getFullYear();
            const mes = hoy.getMonth() - fecha.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < fecha.getDate())) {
                edad--;
            }
            if (edad < 18) {
                errores.fecha_nacimiento = 'Debes tener al menos 18 años.';
            }
        }
        
        if (direccion === '') errores.direccion = 'Este campo es obligatorio.';
        
        if (password === '') {
            errores.password = 'Este campo es obligatorio.';
        } 
        
        else if (password.length < 7 || !/\d/.test(password) || !/[a-zA-Z]/.test(password)) {
            errores.password = 'Debe tener al menos 7 caracteres, una letra y un número.';
        } 
        
        else if (password !== confirm_password) {
            errores.password = 'Las contraseñas no coinciden.';
        }
        
        if (Object.keys(errores).length > 0) {
            e.preventDefault(); // Cancela el envío
            for (const campo in errores) {
                document.getElementById('error-' + campo).textContent = errores[campo]; // Muestra los errores
            }
        }
    });
    </script>
</body>
</html>