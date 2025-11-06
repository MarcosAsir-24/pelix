<?php
session_start();
// Solo permitir admins
$es_admin = false;
if (isset($_SESSION['usuario_id'])) {
    $mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
    $uid = intval($_SESSION['usuario_id']);
    $res = $mysqli->query("SELECT admin FROM usuarios WHERE id=$uid LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $es_admin = !empty($row['admin']) && $row['admin'] == 1;
    }
    $mysqli->close();
}
if (!$es_admin) {
    echo "<div style='color:red;text-align:center;margin:40px;'>Acceso solo para administradores.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $categoria_id = intval($_POST['categoria_id']);
    $duracion = intval($_POST['duracion']);
    $sinopsis = trim($_POST['sinopsis']);
    $destacada = isset($_POST['destacada']) ? 1 : 0;
    $trailer_url = trim($_POST['trailer_url']);

    // Subida de imagen de película
    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $permitidas)) {
            $nombre_img = uniqid('peli_') . '.' . $ext;
            $ruta_img = __DIR__ . '/img/peliculas/' . $nombre_img;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_img)) {
                $imagen = 'img/peliculas/' . $nombre_img;
            }
        }
    }

    // Subir video
    $video_url = '';
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
        $permitidas = ['mp4', 'webm', 'mkv'];
        if (in_array($ext, $permitidas)) {
            // La película se sube a la carpeta /videos/
            $nombre_video = uniqid('video_') . '.' . $ext;
            $ruta_video = __DIR__ . '/videos/' . $nombre_video;
            if (!is_dir(__DIR__ . '/videos/')) {
                mkdir(__DIR__ . '/videos/', 0777, true);
            }
            if (move_uploaded_file($_FILES['video']['tmp_name'], $ruta_video)) {
                $video_url = 'videos/' . $nombre_video;
            }
        }
    }

    // Guardar peli en la base de datos
    $mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
    if (!$mysqli->connect_errno && $imagen && $titulo && $categoria_id && $duracion && $sinopsis) {
        // Si no se ha subido vídeo, $video_url estará vacío y se guardará como NULL
        $video_url_db = $video_url !== '' ? $video_url : null;
        $stmt = $mysqli->prepare("INSERT INTO peliculas (titulo, categoria_id, imagen, duracion, sinopsis, destacada, trailer_url, video_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisissss", $titulo, $categoria_id, $imagen, $duracion, $sinopsis, $destacada, $trailer_url, $video_url_db);
        if ($stmt->execute()) {
            echo "<div style='color:#FFD54F;text-align:center;margin:30px;'>Película subida correctamente.</div>";
        } else {
            echo "<div style='color:red;text-align:center;margin:30px;'>Error al guardar en la base de datos.</div>";
        }
        $stmt->close();
    } else {
        echo "<div style='color:red;text-align:center;margin:30px;'>Error en los datos o la subida.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Película</title>
    <style>
        body { background: #111; color: #FFD54F; font-family: Arial; }
        .form-container { max-width: 520px; margin: 40px auto; background: #181818; padding: 32px; border-radius: 14px; border: 2px solid #FFD54F; }
        label { display: block; margin-top: 18px; color: #FFD54F; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #FFD54F; background: #222; color: #FFD54F; margin-top: 6px; }
        input[type="checkbox"] { width: auto; }
        button { margin-top: 24px; background: #FFD54F; color: #181818; border: none; border-radius: 8px; padding: 12px 32px; font-weight: bold; font-size: 1.1em; cursor: pointer; }
        button:hover { background: #bb860b; color: #fff; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Subir nueva película</h2>
        <form method="post" enctype="multipart/form-data">
            <label>Título
                <input type="text" name="titulo" required>
            </label>
            <label>Categoría
                <select name="categoria_id" required>
                    <?php
                    $mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
                    $res = $mysqli->query("SELECT id, nombre FROM categorias ORDER BY nombre");
                    while ($cat = $res->fetch_assoc()) {
                        echo '<option value="' . $cat['id'] . '">' . htmlspecialchars($cat['nombre']) . '</option>';
                    }
                    ?>
                </select>
            </label>
            <label>Duración (minutos)
                <input type="number" name="duracion" min="1" required>
            </label>
            <label>Sinopsis
                <textarea name="sinopsis" rows="4" required></textarea>
            </label>
            <label>Imagen de portada
                <input type="file" name="imagen" accept="image/*" required>
            </label>
            <label>Tráiler (URL de YouTube)
                <input type="url" name="trailer_url" placeholder="https://www.youtube.com/watch?v=...">
            </label>
            <label>Vídeo completo (archivo grande)
                <input type="file" name="video" accept="video/mp4,video/webm,video/mkv">
            </label>
            <label>
                <input type="checkbox" name="destacada" value="1"> Marcar como destacada
            </label>
            <button type="submit">Subir película</button>
        </form>
    </div>
</body>
</html>
