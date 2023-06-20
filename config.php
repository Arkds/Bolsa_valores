<?php
// Configuración de la base de datos
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'bv';

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Establecer la codificación de caracteres
$conn->set_charset('utf8mb4');
?>
