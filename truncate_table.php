<?php
include 'config.php';

$sql = "TRUNCATE TABLE stock_data";
$conn->query($sql);

$response = ['message' => 'Table truncated successfully'];

echo json_encode($response);

$conn->close();
?>
