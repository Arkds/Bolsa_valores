<?php
session_start();
include 'config.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    exit('No se ha iniciado sesión');
}

$usuarioId = $_SESSION['usuario_id'];

// Obtener los datos de la transacción
$descripcion = $_POST['descripcion'];
$monto = $_POST['monto'];
$fecha = $_POST['fecha'];

// Insertar la transacción en la base de datos
$sqlInsert = "INSERT INTO transacciones (usuario_id, descripcion, monto, fecha) VALUES ('$usuarioId', '$descripcion', '$monto', '$fecha')";
if ($conn->query($sqlInsert) === TRUE) {
    $response = array('status' => 'success', 'message' => 'Transacción guardada correctamente');
} else {
    $response = array('status' => 'error', 'message' => 'Error al guardar la transacción en la base de datos');
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
