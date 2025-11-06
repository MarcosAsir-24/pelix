<?php
// Uso: accede a este archivo desde el navegador y pon la contraseña en la URL, por ejemplo:
// http://localhost/TFG/password_hash.php?pass=miclave
$pass = $_GET['pass'] ?? '';
if ($pass) {
    echo "Hash para '$pass':<br>";
    echo password_hash($pass, PASSWORD_DEFAULT);
} else {
    echo '<form><input name="pass" placeholder="Contraseña"><button>Generar hash</button></form>';
}
