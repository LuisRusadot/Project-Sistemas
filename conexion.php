<?php
$host = "localhost";
$usuario = "root";
$contrasena = "1234"; // Cambia esto por tu contraseña real de MySQL o déjalo vacío si no tiene
$bd = "banco_hojas_vida";

$conn = new mysqli($host, $usuario, $contrasena, $bd);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
?>
