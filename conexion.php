<?php
$conexion = mysqli_connect("localhost", "root", "rootroot", "peliculas");

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>