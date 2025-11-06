<?php
require_once 'conexion.php';
header('Content-Type: application/json');

function empieza_con($texto, $inicio) {
    return substr($texto, 0, strlen($inicio)) === $inicio;
}

if (isset($_GET['buscar'])) {
    $buscar = trim($_GET['buscar']);
    if ($buscar !== '') {
        // Buscar por título
        $stmt = $conexion->prepare("SELECT DISTINCT id, titulo, imagen FROM peliculas WHERE titulo LIKE ? LIMIT 10");
        $like = "%" . $buscar . "%";
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $peliculas = [];

        // Coger ruta de la imagen
        while ($fila = $resultado->fetch_assoc()) {
            $imagenRuta = trim($fila['imagen']);
            if (!empty($imagenRuta) && !empieza_con($imagenRuta, 'img/')) {
                $imagenRuta = 'img/' . $imagenRuta;
            }
            $peliculas[] = [
                'id' => $fila['id'],
                'titulo' => htmlspecialchars($fila['titulo'], ENT_QUOTES, 'UTF-8'),
                'imagen' => $imagenRuta
            ];
        }

        echo json_encode($peliculas);
        exit;
    }
}

// Si no se envió una búsqueda válida devuelve un array vacio
echo json_encode([]);
