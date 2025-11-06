<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color:#FFD54F;text-align:center;margin-top:40px;'>Debes iniciar sesión para ver tus favoritos.</p>";
    exit;
}

$usuario_id = intval($_SESSION['usuario_id']);
$mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
if ($mysqli->connect_errno) {
    echo "<p style='color:red;'>Error de conexión a la base de datos.</p>";
    exit;
}

$sql = "SELECT p.id, p.titulo, p.imagen
        FROM favoritos f
        JOIN peliculas p ON f.pelicula_id = p.id
        WHERE f.usuario_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Favoritos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background: #000;
        }
        .volver-btn {
            display: inline-block;
            margin: 32px auto 0 auto;
            padding: 14px 36px;
            background: #FFD54F;
            color: #181818;
            border: none;
            border-radius: 8px;
            font-size: 1.15em;
            font-weight: bold;
            text-decoration: none;
            box-shadow: 0 2px 12px rgba(0,0,0,0.18);
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-align: center;
        }
        .volver-btn:hover {
            background: #bb860b;
            color: #fff;
            box-shadow: 0 4px 18px #FFD54F44;
        }
    </style>
</head>
<body>
    <div style="max-width:1100px;margin:40px auto 0 auto;padding:32px 18px 18px 18px;background:#181818;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.35);border:2px solid #FFD54F;">
        <h2 style="color:#FFD54F;text-align:center;font-size:2em;margin-bottom:32px;letter-spacing:1px;"><i class="fas fa-heart"></i> Lista de Favoritos</h2>
        <?php
        if ($res->num_rows === 0) {
            echo "<p style='color:#FFD54F;text-align:center;margin-top:40px;font-size:1.2em;'>No tienes películas en favoritos todavía.</p>";
        } else {
            echo '<div style="display:flex;flex-wrap:wrap;gap:32px;justify-content:center;margin:40px 0;">';
            while ($row = $res->fetch_assoc()) {
                echo '<a href="/TFG/peliculas/pelicula.php?pelicula=' . $row['id'] . '" style="text-decoration:none;color:inherit;text-align:center;width:160px;transition:transform 0.15s;">';
                echo '<img src="/TFG/' . htmlspecialchars($row['imagen']) . '" alt="' . htmlspecialchars($row['titulo']) . '" style="width:140px;height:200px;object-fit:cover;border-radius:10px;box-shadow:0 2px 8px #000;margin-bottom:10px;border:2px solid #FFD54F;background:#222;">';
                echo '<div style="color:#FFD54F;font-weight:bold;font-size:1.05em;">' . htmlspecialchars($row['titulo']) . '</div>';
                echo '</a>';
            }
            echo '</div>';
        }
        ?>
        <div style="width:100%;text-align:center;">
            <a href="/TFG/pagina_principal.php" class="volver-btn">
                <i class="fas fa-arrow-left"></i> Volver a inicio
            </a>
        </div>
    </div>
</body>
</html>
<?php
$stmt->close();
$mysqli->close();
?>
