<?php
// Datos de conexión a la base de datos
$host = "localhost";
$usuario = "root";
$password = "usbw";
$base_de_datos = "13501_elg_labiastech";

// Crear la conexión
$conexion = new mysqli($host, $usuario, $password, $base_de_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer el conjunto de caracteres a UTF-8
$conexion->set_charset("utf8");
?>