<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['usuario_id']) || !isset($_GET['pelicula_id'])) {
    echo json_encode(['favorito' => false]);
    exit;
}
$usuario_id = intval($_SESSION['usuario_id']);
$pelicula_id = intval($_GET['pelicula_id']);
$mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
$res = $mysqli->prepare("SELECT 1 FROM favoritos WHERE usuario_id=? AND pelicula_id=?");
$res->bind_param("ii", $usuario_id, $pelicula_id);
$res->execute();
$res->store_result();
echo json_encode(['favorito' => $res->num_rows > 0]);
$res->close();
$mysqli->close();
