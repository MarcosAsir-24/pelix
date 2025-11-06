<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_POST['pelicula_id'])) {
    http_response_code(400); // El codigo del error es "Bad request"
    exit();
}

$usuario_id = intval($_SESSION['usuario_id']);
$pelicula_id = intval($_POST['pelicula_id']);
$mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
if (!$mysqli->connect_errno) {
    $mysqli->query("DELETE FROM favoritos WHERE usuario_id=$usuario_id AND pelicula_id=$pelicula_id");
}

http_response_code(200); // Si todo va bien te devuelve un "operacion exitosa"
