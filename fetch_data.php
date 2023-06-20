<?php
include 'config.php';

// Eliminar registros antiguos si hay más de 40
$maxRecords = 40;
$deleteQuery = "DELETE FROM stock_data WHERE id NOT IN (SELECT id FROM (SELECT id FROM stock_data ORDER BY created_at DESC LIMIT $maxRecords) AS x)";
$conn->query($deleteQuery);

// Obtener los datos actualizados
$sql = 'SELECT * FROM stock_data ORDER BY created_at DESC LIMIT 50';
$result = $conn->query($sql);

$data = [
  'labels' => [],
  'sp500' => [],
  'dowJones' => []
];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $data['labels'][] = $row['label'];
    $data['sp500'][] = $row['sp500'];
    $data['dowJones'][] = $row['dow_jones'];
  }
}

echo json_encode($data);

$conn->close();
?>
