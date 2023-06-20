<?php
session_start();
include 'config.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}

$usuarioId = $_SESSION['usuario_id'];

// Lógica de simulación de trading...

// Generar un registro de transacción ficticio
$descripciones = array("Compra ", "Venta ");
$descripcion = $descripciones[array_rand($descripciones)];
$monto = rand(21000, 32000);


// Insertar la transacción en la base de datos
$sqlInsert = "INSERT INTO transacciones (usuario_id, descripcion, monto) VALUES ('$usuarioId', '$descripcion', '$monto')";
$resultInsert = $conn->query($sqlInsert);

if ($resultInsert) {
  // Éxito al insertar la transacción
  $response = array(
    'status' => 'success',
    'message' => 'Transacción guardada correctamente.'
  );
} else {
  // Error al insertar la transacción
  $response = array(
    'status' => 'error',
    'message' => 'Error al guardar la transacción.'
  );
}

$conn->close();

// Convertir la respuesta a formato JSON
$jsonResponse = json_encode($response);

// Enviar la respuesta JSON al cliente
header('Content-Type: application/json');
echo $jsonResponse;
?>
