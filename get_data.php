<?php
session_start();
include 'config.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuarioId = $_SESSION['usuario_id'];

// Obtener el nombre del usuario
$sqlUsuario = "SELECT nombre FROM usuarios WHERE id = $usuarioId";
$resultUsuario = $conn->query($sqlUsuario);
$nombreUsuario = $resultUsuario->fetch_assoc()['nombre'];



// Obtener página actual
$page = $_POST['page'];

// Número de resultados por página
$resultsPerPage = $_POST['resultsPerPage'];

// Calcular el índice de inicio
$startFrom = ($page - 1) * $resultsPerPage;


// Consultar a la base de datos
$query = "SELECT * FROM transacciones ORDER BY id ASC LIMIT $startFrom, $resultsPerPage";
$result = $conn->query($query);

// Generar tabla con resultados
if ($result->num_rows > 0) {
    echo "<table class='table'>";
    echo "<tr><th>Descripcion</th><th>Monto</th><th>Fecha</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" .$row['descripcion'] . "</td><td>" . number_format($row['monto'], 2) . "</td><td>" . date('d/m/Y H:i:s', strtotime($row['fecha'])) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron resultados.";
}

// Calcular el número total de páginas
$queryCount = "SELECT COUNT(*) AS count FROM transacciones";
$resultCount = $conn->query($queryCount);
$rowCount = $resultCount->fetch_assoc();
$totalPages = ceil($rowCount["count"] / $resultsPerPage);

// Generar enlaces de paginación
echo "<div id='pagination' class='pagination justify-content-center'>";
if ($page > 1) {
    echo "<a class='btn btn-primary mr-2' href='javascript:void(0);' onclick='loadTableData(" . ($page - 1) . ")'>&laquo; Anterior</a>";
}
for ($i = 1; $i <= $totalPages; $i++) {
    echo "<a class='btn btn-primary mr-2' href='javascript:void(0);' onclick='loadTableData($i)'>$i</a>";
}
if ($page < $totalPages) {
    echo "<a class='btn btn-primary' href='javascript:void(0);' onclick='loadTableData(" . ($page + 1) . ")'>Siguiente &raquo;</a>";
}
echo "</div>";

// Cerrar conexión
$conn->close();
?>
