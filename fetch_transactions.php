<?php
session_start();
include 'config.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

$usuarioId = $_SESSION['usuario_id'];

// Obtener el historial de transacciones del usuario
$sqlTransacciones = "SELECT * FROM transacciones WHERE usuario_id = $usuarioId ORDER BY fecha DESC";
$resultTransacciones = $conn->query($sqlTransacciones);
$transacciones = $resultTransacciones->fetch_all(MYSQLI_ASSOC);

$conn->close();

// Convertir el resultado a formato JSON
$jsonResponse = json_encode($transacciones);

// Enviar la respuesta JSON al cliente
header('Content-Type: application/json');
echo $jsonResponse;
?>
