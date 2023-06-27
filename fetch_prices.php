<?php
include 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consultar los precios más recientes desde la tabla stock_data
$sql = "SELECT sp500, dow_jones FROM stock_data ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtener los valores de los precios
    $row = $result->fetch_assoc();
    $sp500 = $row["sp500"];
    $dowJones = $row["dow_jones"];
    
    // Construir un arreglo con los precios
    $prices = array(
        "sp500" => $sp500,
        "dowJones" => $dowJones
    );
    
    // Devolver los precios en formato JSON
    header("Content-Type: application/json");
    echo json_encode($prices);
} else {
    echo "No se encontraron precios en la base de datos.";
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
