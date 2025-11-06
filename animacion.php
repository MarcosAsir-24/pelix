<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/conexion.php';

$query_categoria = "SELECT id, nombre, icono FROM categorias WHERE nombre = 'Animación'";
$result_categoria = mysqli_query($conexion, $query_categoria);

$categoria = mysqli_fetch_assoc($result_categoria);
$cat_id = (int)$categoria['id'];
$cat_nombre = $categoria['nombre'];
$cat_icono = $categoria['icono'];

$query_pelis = "SELECT id, titulo, imagen FROM peliculas WHERE categoria_id = $cat_id ORDER BY id DESC";
$result_pelis = mysqli_query($conexion, $query_pelis);

$peliculas = [];
while ($peli = mysqli_fetch_assoc($result_pelis)) {
    $peliculas[] = $peli;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PELIX | Animación</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="css/estilo.css">
    <style>
        .seccion-proximamente-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-family: 'Segoe UI', sans-serif;
        }

        .seccion-proximamente-header h2 {
            color: #FFD54F;
            font-weight: bold;
            font-size: 26px;
            user-select: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .seccion-proximamente-header h2 img {
            height: 30px;
        }

        .galeria-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* Fuerza 5 por fila */
            gap: 30px;
            padding-bottom: 30px;
        }

        .pelicula-prox {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            text-align: center;
        }

        .pelicula-prox h3 {
            margin-top: 12px;
            color: #FFD54F;
            font-size: 18px;
            font-weight: bold;
            user-select: none;
        }

        .imagen-con-icono {
            position: relative;
            cursor: pointer;
        }

        .imagen-con-icono img {
            width: 100%;
            height: 200px;
            object-fit: cover; 
            border-radius: 8px;
        }

        .icono-play {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 38px;
            color: rgba(255, 255, 255, 0.8);
            transition: transform 0.2s ease;
        }

        .imagen-con-icono:hover .icono-play {
            transform: translate(-50%, -50%) scale(1.1);
            color: #FFD54F;
        }
    </style>
</head>
<body>

<?php include 'includes/cabecera.php'; ?>
<?php include 'includes/panel.php'; ?>

<div class="contenido">
    <div class="seccion-proximamente-header" style="margin-top: 20px;">
        <h2>
            <img src="<?= htmlspecialchars($cat_icono) ?>" alt="Animación">
            <?= htmlspecialchars($cat_nombre) ?>
        </h2>
    </div>
    <div class="galeria-grid">
        <?php foreach ($peliculas as $peli): ?>
            <div class="pelicula-prox">
                <div class="imagen-con-icono">
                    <a href="/TFG/peliculas/pelicula.php?pelicula=<?= $peli['id'] ?>">
                        <img src="<?= htmlspecialchars($peli['imagen']) ?>" alt="<?= htmlspecialchars($peli['titulo']) ?>">
                        <i class="fas fa-play-circle icono-play"></i>
                    </a>
                </div>
                <h3><?= htmlspecialchars($peli['titulo']) ?></h3>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
