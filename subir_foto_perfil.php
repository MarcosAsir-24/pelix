<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_POST['usuario_id']) || $_SESSION['usuario_id'] != $_POST['usuario_id']) {
    header("Location: pagina_principal.php");
    exit();
}
$usuario_id = intval($_SESSION['usuario_id']);
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['foto_perfil']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $permitidas)) {
        $nombre_archivo = 'perfil_' . $usuario_id . '_' . time() . '.' . $ext;
        $destino = __DIR__ . '/img/perfiles/' . $nombre_archivo;
        if (!is_dir(__DIR__ . '/img/perfiles/')) {
            mkdir(__DIR__ . '/img/perfiles/', 0777, true);
        }
        if (move_uploaded_file($tmp, $destino)) {
            $mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
            if (!$mysqli->connect_errno) {
                // Borra la anterior solo si es distinta y existe
                $res = $mysqli->query("SELECT foto_perfil FROM usuarios WHERE id=$usuario_id");
                if ($res && $row = $res->fetch_assoc()) {
                    $anterior = $row['foto_perfil'];
                    if ($anterior && $anterior !== $nombre_archivo && file_exists(__DIR__ . '/img/perfiles/' . $anterior)) {
                        unlink(__DIR__ . '/img/perfiles/' . $anterior);
                    }
                }
                $mysqli->query("UPDATE usuarios SET foto_perfil='" . $mysqli->real_escape_string($nombre_archivo) . "' WHERE id=$usuario_id");
            }
            header("Location: pagina_principal.php?perfil=1&v=" . time());
            exit();
        }
    }
}
header("Location: pagina_principal.php?perfil=1&foto=error");
exit();
