<?php
include 'config.php';

$label = $_POST['label'];
$sp500 = $_POST['sp500'];
$dowJones = $_POST['dowJones'];

$sql = "INSERT INTO stock_data (label, sp500, dow_jones) VALUES ('$label', $sp500, $dowJones)";
$conn->query($sql);

$response = ['message' => 'Data inserted successfully'];

echo json_encode($response);

$conn->close();
?>
