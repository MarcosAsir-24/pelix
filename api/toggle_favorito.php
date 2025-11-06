<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['usuario_id']) || !isset($_POST['pelicula_id'])) {
    echo json_encode(['status' => 'error']);
    exit;
}
$usuario_id = intval($_SESSION['usuario_id']);
$pelicula_id = intval($_POST['pelicula_id']);
$mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");

// ¿Ya es favorito?
$res = $mysqli->prepare("SELECT 1 FROM favoritos WHERE usuario_id=? AND pelicula_id=?");
$res->bind_param("ii", $usuario_id, $pelicula_id);
$res->execute();
$res->store_result();
if ($res->num_rows > 0) {
    // Quitar de favoritos
    $del = $mysqli->prepare("DELETE FROM favoritos WHERE usuario_id=? AND pelicula_id=?");
    $del->bind_param("ii", $usuario_id, $pelicula_id);
    $del->execute();
    echo json_encode(['status' => 'removed']);
    $del->close();
} else {
    // Añadir a favoritos
    $add = $mysqli->prepare("INSERT INTO favoritos (usuario_id, pelicula_id) VALUES (?, ?)");
    $add->bind_param("ii", $usuario_id, $pelicula_id);
    $add->execute();
    echo json_encode(['status' => 'added']);
    $add->close();
}
$res->close();
$mysqli->close();
