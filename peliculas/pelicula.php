<?php
session_start();

$mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$id = isset($_GET['pelicula']) ? intval($_GET['pelicula']) : 0;
if ($id <= 0) {
    die("Película no encontrada.");
}

// Consultar la peli
$stmt = $mysqli->prepare("SELECT p.*, c.nombre AS categoria FROM peliculas p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$peli = $result->fetch_assoc();

if (!$peli) {
    die("Película no encontrada.");
}

// Comprobar si el usuario está suscrito y si es admin
$usuario_suscrito = false;
$usuario_admin = false;
if (isset($_SESSION['usuario_id'])) {
    $uid = intval($_SESSION['usuario_id']);
    $res = $mysqli->query("SELECT premium, admin FROM usuarios WHERE id=$uid LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $usuario_suscrito = $row['premium'] == 1;
        $usuario_admin = !empty($row['admin']) && $row['admin'] == 1;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($peli['titulo']) ?> - Detalles</title>
    <link rel="stylesheet" href="/TFG/css/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            background: #000;
            color: #fff;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 48px auto 32px auto;
            background: #181818;
            padding: 38px 32px 32px 32px;
            border-radius: 18px;
            border: 2px solid #FFD54F;
            box-shadow: 0 8px 32px rgba(0,0,0,0.35);
        }
        h1 {
            color: #FFD54F;
            font-size: 2.2em;
            margin-bottom: 18px;
            letter-spacing: 1px;
            text-shadow: 0 2px 12px #000;
        }
        .info {
            display: flex;
            gap: 38px;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .info img {
            width: 260px;
            min-width: 180px;
            border-radius: 12px;
            border: 2.5px solid #FFD54F;
            box-shadow: 0 4px 24px #000a;
            background: #222;
        }
        .datos {
            flex: 1;
            min-width: 220px;
        }
        .datos p {
            margin: 12px 0;
            font-size: 1.08em;
        }
        .datos strong {
            color: #FFD54F;
        }
        .destacada {
            color: #FFD54F;
            font-weight: bold;
            margin-top: 18px;
            font-size: 1.1em;
            letter-spacing: 0.5px;
        }
        .video-container {
            margin-top: 38px;
            text-align: center;
        }
        .video-container iframe,
        .video-container video {
            width: 92%;
            max-width: 720px;
            height: 410px;
            border-radius: 14px;
            border: 2.5px solid #FFD54F;
            background: #000;
            box-shadow: 0 4px 24px #000a;
        }
        .aviso {
            color: #FFD54F;
            margin-top: 22px;
            text-align: center;
            font-size: 1.08em;
        }
        .volver {
            display: inline-block;
            margin-top: 38px;
            color: #FFD54F;
            background: #222;
            border: 2px solid #FFD54F;
            border-radius: 8px;
            padding: 12px 32px;
            font-weight: bold;
            font-size: 1.08em;
            text-decoration: none;
            transition: background 0.2s, color 0.2s, border 0.2s;
            box-shadow: 0 2px 12px #000a;
        }
        .volver:hover {
            background: #FFD54F;
            color: #181818;
            border-color: #FFD54F;
        }
        .icono-categoria {
            color: #FFD54F;
            margin-right: 8px;
            font-size: 1.1em;
        }
        .like-btn {
            background: none;
            border: none;
            cursor: pointer;
            outline: none;
            font-size: 2.1em;
            color: #FFD54F;
            transition: color 0.2s, transform 0.2s;
            margin-bottom: 18px;
        }
        .like-btn.liked {
            color: #e53935;
            transform: scale(1.15);
        }
        .like-btn:hover {
            color: #ff1744;
        }
        .like-btn .fa-heart {
            pointer-events: none;
        }
        .like-label {
            color: #FFD54F;
            font-size: 1em;
            margin-bottom: 10px;
            display: block;
            text-align: center;
        }
        @media (max-width: 700px) {
            .container { padding: 18px 4vw; }
            .info { flex-direction: column; align-items: center; gap: 18px; }
            .info img { width: 90vw; max-width: 320px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-film"></i> <?= htmlspecialchars($peli['titulo']) ?></h1>
        <div style="text-align:center;margin-bottom:32px;">
            <img src="/TFG/<?= htmlspecialchars($peli['imagen']) ?>" alt="<?= htmlspecialchars($peli['titulo']) ?>" style="max-width:320px;width:90vw;border-radius:14px;border:2.5px solid #FFD54F;box-shadow:0 4px 24px #000a;background:#222;">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <form id="likeForm" method="post" style="margin-top:18px;">
                    <input type="hidden" name="pelicula_id" value="<?= $peli['id'] ?>">
                    <button type="button" class="like-btn" id="likeBtn" title="Añadir a favoritos">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <span class="like-label" id="likeLabel" style="display:none;">Añadido a favoritos</span>
                </form>
                <script>
                document.addEventListener('DOMContentLoaded', function() { // Espera a que este cargado todo el HTML
                    var likeBtn = document.getElementById('likeBtn');
                    var likeLabel = document.getElementById('likeLabel');
                    var peliculaId = <?= json_encode($peli['id']) ?>;
                    var liked = false;

                    // Comprobar si ya está en favoritos la peli
                    fetch('/TFG/api/check_favorito.php?pelicula_id=' + peliculaId)
                        .then(res => res.json())
                        .then(data => {
                            if (data.favorito) {
                                likeBtn.classList.add('liked');
                                likeBtn.innerHTML = '<i class="fa-solid fa-heart"></i>';
                                likeLabel.textContent = 'En favoritos';
                                likeLabel.style.display = 'block';
                                liked = true;
                            }
                        });

                    // Funcion para añadir o quitar de favoritos
                    likeBtn.addEventListener('click', function(e) { 
                        e.preventDefault(); // Evita que el formulario se envíe
                        fetch('/TFG/api/toggle_favorito.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: 'pelicula_id=' + encodeURIComponent(peliculaId)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'added') {
                                likeBtn.classList.add('liked');
                                likeBtn.innerHTML = '<i class="fa-solid fa-heart"></i>';
                                likeLabel.textContent = 'Añadido a favoritos';
                                likeLabel.style.display = 'block';
                                liked = true;
                            } 
                            
                            else if (data.status === 'removed') {
                                likeBtn.classList.remove('liked');
                                likeBtn.innerHTML = '<i class="fa-regular fa-heart"></i>';
                                likeLabel.textContent = '';
                                likeLabel.style.display = 'none';
                                liked = false;
                            }
                        });
                    });
                });
                </script>
            <?php endif; ?>
        </div>
        <div class="info">
            <!-- <img src="/TFG/<?= htmlspecialchars($peli['imagen']) ?>" alt="<?= htmlspecialchars($peli['titulo']) ?>"> -->
            <div class="datos">
                <p><span class="icono-categoria"><i class="fas fa-tags"></i></span><strong>Categoría:</strong> <?= htmlspecialchars($peli['categoria']) ?></p>
                <p><span class="icono-categoria"><i class="fas fa-clock"></i></span><strong>Duración:</strong> <?= intval($peli['duracion']) ?> min</p>
                <p><span class="icono-categoria"><i class="fas fa-align-left"></i></span><strong>Sinopsis:</strong><br><?= nl2br(htmlspecialchars($peli['sinopsis'])) ?></p>
                <?php if ($peli['destacada']): ?>
                    <div class="destacada"><i class="fas fa-star"></i> ¡Película destacada!</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="video-container">
            <?php
            
            // Convierte la URL de YouTube a formato embed si se necesita
            $trailer_url = $peli['trailer_url'];
            if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $trailer_url, $matches)) {
                $trailer_url = 'https://www.youtube.com/embed/' . $matches[1];
            } 
            
            elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $trailer_url, $matches)) {
                $trailer_url = 'https://www.youtube.com/embed/' . $matches[1];
            } 
            ?>
            
            <?php if ($usuario_suscrito || $usuario_admin): ?>
                <?php if (!empty($peli['video_url'])): ?>  
                    <video controls poster="/TFG/<?= htmlspecialchars($peli['imagen']) ?>">
                        <source src="/TFG/<?= htmlspecialchars($peli['video_url']) ?>" type="video/mp4">
                        Tu navegador no soporta el vídeo.
                    </video>
                    
                <?php else: ?>
                    <div class="aviso"><i class="fas fa-exclamation-triangle"></i> Aún no ha llegado esta pelicula a nuestro repertorio.</div>
                <?php endif; ?>
                <?php if (!empty($peli['trailer_url'])): ?>
                    <div style="margin-top:30px;">
                        <h3 style="color:#FFD54F;margin-bottom:10px;"><i class="fas fa-play-circle"></i> Tráiler</h3>
                        <iframe src="<?= htmlspecialchars($trailer_url) ?>" frameborder="0" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>
                <?php if ($usuario_admin): ?>
                    <div class="aviso" style="margin-top:18px;color:#FFD54F;font-size:1.1em;">
                        <i class="fas fa-user-shield"></i> Estás viendo como administrador.
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php if (!empty($peli['trailer_url'])): ?>
                    <iframe src="<?= htmlspecialchars($trailer_url) ?>" frameborder="0" allowfullscreen></iframe>
                <?php else: ?>
                    <div class="aviso"><i class="fas fa-exclamation-triangle"></i> No hay tráiler disponible.</div>
                <?php endif; ?>
                <div class="aviso">Para ver la película completa, <a href="/TFG/micuenta.php" style="color:#FFD54F;text-decoration:underline;">suscríbete</a> a PELIX.</div>
            <?php endif; ?>
        </div>
        <div style="text-align:center;">
            <a href="/TFG/pagina_principal.php" class="volver"><i class="fas fa-arrow-left"></i> Volver al listado</a>
        </div>
    </div>
</body>
</html>
