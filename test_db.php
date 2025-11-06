
<?php
// Prueba rápida en un archivo test_db.php
require_once 'conexion.php';
try {
    $stmt = $conexion->query("SELECT 1");
    echo "Conexión OK";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>