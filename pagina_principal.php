<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/conexion.php';

$categorias = [];
$query_categorias = "SELECT id, nombre, icono FROM categorias WHERE nombre IN ('Próximamente', 'Acción', 'Comedia', 'Terror', 'Ciencia Ficción', 'Animación')";
$result_categorias = mysqli_query($conexion, $query_categorias);

while ($cat = mysqli_fetch_assoc($result_categorias)) {
    $cat_id = (int)$cat['id'];
    $cat_nombre = $cat['nombre'];
    $cat_icono = $cat['icono'];

    $query_pelis = "SELECT id, titulo, imagen FROM peliculas WHERE categoria_id = $cat_id ORDER BY RAND() LIMIT 5";
    $result_pelis = mysqli_query($conexion, $query_pelis);

    $peliculas = [];
    while ($peli = mysqli_fetch_assoc($result_pelis)) {
        $peliculas[] = $peli;
    }

    if (!empty($peliculas)) {
        $categorias[$cat_nombre] = [
            'icono' => $cat_icono,
            'peliculas' => $peliculas
        ];
    }
}

// Función para quitar tildes
function quitar_tildes($cadena) {
    $originales = array('á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ');
    $modificadas = array('a','e','i','o','u','A','E','I','O','U','n','N');
    return str_replace($originales, $modificadas, $cadena);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PELIX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        .seccion-proximamente-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        .seccion-proximamente-header h2 {
            color: #FFD54F;
            font-weight: bold;
            margin: 0;
            font-size: 24px;
            user-select: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .seccion-proximamente-header h2 img {
            height: 28px;
        }

        .seccion-proximamente-header a {
            font-weight: bold;
            color: #FFD54F;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s ease, text-shadow 0.3s ease;
            user-select: none;
        }

        .seccion-proximamente-header a:hover {
            color: #FFF176;
            text-shadow: 0 0 6px rgba(255, 241, 118, 0.9);
        }

        .proximamente-galeria {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: nowrap;
            overflow-x: auto;
            padding-bottom: 20px;
            margin-top: 0;
        }

        .pelicula-prox {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            text-align: center;
            flex: 0 0 auto;
        }

        .pelicula-prox h3 {
            margin-top: 12px;
            color: #FFD54F;
            font-size: 18px;
            font-weight: bold;
            user-select: none;
        }
    </style>
</head>
<body>

    <?php
    echo "<!-- Debug: Antes de incluir cabecera -->";
    if (!@include 'includes/cabecera.php') {
        echo "<div style='color:red'>Error: No se pudo incluir includes/cabecera.php</div>";
    } else {
        echo "<!-- Debug: cabecera incluida correctamente -->";
    }
    ?>

    <?php
    echo "<!-- Debug: Antes de incluir panel -->";
    if (!@include 'includes/panel.php') {
        echo "<div style='color:red'>Error: No se pudo incluir includes/panel.php</div>";
    } else {
        echo "<!-- Debug: panel incluido correctamente -->";
    }
    ?>

    <div class="contenido">
        <?php foreach ($categorias as $nombre => $data): ?>
            <div class="seccion-proximamente-header" style="margin-top: <?= $nombre === 'Próximamente' ? '0' : '50px' ?>;">
                <h2>
                    <img src="<?= htmlspecialchars($data['icono']) ?>" alt="<?= htmlspecialchars($nombre) ?>">
                    <?= htmlspecialchars($nombre) ?>
                </h2>
                <a href="<?php
                    $nombre_sin_tilde = strtolower(quitar_tildes(str_replace(' ', '', $nombre)));
                    echo $nombre_sin_tilde . '.php';
                ?>">Ver todo ▶</a>
            </div>
            <div class="proximamente-galeria">
                <?php foreach ($data['peliculas'] as $peli): ?>
                    <div class="pelicula-prox">
                        <div class="imagen-con-icono">
                            <a href="/TFG/peliculas/pelicula.php?pelicula=<?= $peli['id'] ?>">
                                <img src="/TFG/<?= htmlspecialchars($peli['imagen']) ?>" alt="<?= htmlspecialchars($peli['titulo']) ?>">
                                <i class="fas fa-play-circle icono-play"></i>
                            </a>
                        </div>
                        <h3><?= htmlspecialchars($peli['titulo']) ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
