<?php
date_default_timezone_set('America/Lima');
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
$usuarioData = $resultUsuario->fetch_assoc();
$nombreUsuario = $usuarioData['nombre'];


// Obtener el saldo del usuario
$sqlSaldo = "SELECT saldo FROM usuarios WHERE id = $usuarioId";
$resultSaldo = $conn->query($sqlSaldo);
$saldo = $resultSaldo->fetch_assoc()['saldo'];


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="chart.js"></script>
    <script src="jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        .container {
            margin-top: 50px;
        }

        canvas {
            max-width: 100%;
            margin: 0 auto;
            height: 500px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Panel de Control</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Perfil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Cerrar sesión</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container">
    <h2>Bienvenido, <?php echo $nombreUsuario; ?></h2>
    <div class="fluid-container">
        <h3 class="text-center">Gráfico de la Bolsa de Valores</h3>
        <canvas id="stockChart"></canvas>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center">
                    <h4 class="text-white">Saldo: <?php echo $saldo; ?> <span id="saldo"></span></h4>
                    <button class="btn btn-success" id="startButton">Iniciar Trading</button>
                    <button class="btn btn-danger" id="stopButton" disabled>Detener Trading</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4" id="regis">
        <h3>Historial de Transacciones</h3>
        <table class="table table-striped">
            <tbody >
                <div class="container">
            <div class="row">
                
                <div class=" span col-md-3">
                    <select id="resultsPerPage" class="form-control">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div id="tableData"></div>
                </div>
            </div>
        </div>

        
            </tbody>
        </table>

    </div>
    
</div>

<script>


    $(document).ready(function() {
            loadTableData(1);
            $("#resultsPerPage").on("change", function() {
                loadTableData(1);
            });

    });

    function loadTableData(page) {
        var searchInput = $("#searchInput").val();
        var resultsPerPage = $("#resultsPerPage").val();
        $.ajax({
            url: "get_data.php",
            type: "POST",
            data: {page: page, resultsPerPage: resultsPerPage},
            success: function(response) {
                $("#tableData").html(response);
            }
        });
     }




////////////////////////////////////////////////////
    document.getElementById('stopButton').addEventListener('click', function () {
        clearInterval(intervalId); // Detener el intervalo
        document.getElementById('startButton').disabled = false;
        document.getElementById('stopButton').disabled = true;
    });

    document.getElementById('startButton').addEventListener('click', function () {
        intervalId = setInterval(getUpdatedPrices, 1000); // Ejecutar la función getUpdatedPrices cada 5 segundos
        document.getElementById('startButton').disabled = true;
        document.getElementById('stopButton').disabled = false;
    });
     // Función para actualizar el saldo en la interfaz
    function updateBalance(amount) {
        saldo += amount;
        document.getElementById('saldo').textContent = saldo;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('stockChart').getContext('2d');
        var stockData = {
            labels: [],
            datasets: [{
                label: 'S&P 500',
                data: [],
                borderColor: 'red',
                backgroundColor: 'transparent',
            }, {
                label: 'Dow Jones',
                data: [],
                borderColor: 'blue',
                backgroundColor: 'transparent',
            }]
        };

        var stockChart = new Chart(ctx, {
            type: 'line',
            data: stockData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function addDataToChart(label, sp500, dowJones) {
            stockData.labels.push(label);
            stockData.datasets[0].data.push(sp500);
            stockData.datasets[1].data.push(dowJones);

            if (stockData.labels.length > 50) {
                stockData.labels.shift();
                stockData.datasets[0].data.shift();
                stockData.datasets[1].data.shift();
            }

            stockChart.update(); // Actualizar la gráfica con el nuevo valor
        }

        function fetchData() {
            fetch('fetch_data.php')
                .then(response => response.json())
                .then(data => {
                    stockData.labels = data.labels.reverse();
                    stockData.datasets[0].data = data.sp500.reverse();
                    stockData.datasets[1].data = data.dowJones.reverse();

                    stockChart.update(); // Actualizar la gráfica con los datos recuperados
                });
        }

        function generateRandomData() {
            var currentTime = new Date();
            var currentLabel = currentTime.getHours() + ':' + currentTime.getMinutes();
            var newPriceSAndP = getRandomNumberInRange(1000, 5000);
            var newPriceDowJones = getRandomNumberInRange(20000, 33000);

            addDataToChart(currentLabel, newPriceSAndP, newPriceDowJones);

            $.ajax({
                url: 'insert_data.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    label: currentLabel,
                    sp500: newPriceSAndP,
                    dowJones: newPriceDowJones
                },
                success: function (data) {
                    console.log(data);
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }


        var intervalId = setInterval(generateRandomData, 2000); // Generar datos aleatorios cada 5 segundos

        function getRandomNumberInRange(min, max) {
            return Math.floor(Math.random() * (max - min + 1) + min);
        }

        fetchData(); // Obtener los datos iniciales del gráfico desde la base de datos

        setInterval(fetchData, 3000); // Actualizar los datos del gráfico cada 5 segundos
    });

    

    function addTransactionToHistory(transaction) {
        var row = document.createElement('tr');
        var descriptionCell = document.createElement('td');
        var amountCell = document.createElement('td');
        var dateCell = document.createElement('td');

        descriptionCell.textContent = transaction.descripcion;
        amountCell.textContent = transaction.monto;
        dateCell.textContent = transaction.fecha;

        row.appendChild(descriptionCell);
        row.appendChild(amountCell);
        row.appendChild(dateCell);

        loadTableData(1);

        //document.getElementById('transactionHistory').appendChild(row);
    }
    var acciones = 0;
    function buyStocks(price) {
        if (price < 50000 && saldo >= price) {
            acciones += 1;
            saldo -= price;
            updateBalance(-price);

            registerTransaction("Compra de acciones", -price);
        }
    }

    function sellStocks(price) {
        if (price > 5000 && acciones > 0) {
            acciones -= 1;
            saldo += price;
            updateBalance(price);

            registerTransaction("Venta de acciones", +price);
        }
    }

    function robotTrading(newPriceSAndP, newPriceDowJones) {

        // Lógica del robot
        if (newPriceDowJones < 21000 ) {
            buyStocks(newPriceDowJones);

        } else if (newPriceDowJones > 30000) {
            sellStocks(newPriceDowJones);
        }
    }

    function registerTransaction(description, amount) {
        var transaction = {
            descripcion: description,
            monto: amount,
            fecha: new Date().toLocaleString()
        };

        addTransactionToHistory(transaction);

        // Obtén la fecha actual en formato de cadena
        var currentDate = "<?php echo date('Y-m-d H:i:s'); ?>";
        transaction.fecha = currentDate;


        // Llamada AJAX para guardar la transacción en la base de datos
        $.ajax({
            url: 'save_transaction.php', // Ruta del script PHP que guarda la transacción en la base de datos
            type: 'POST',
            dataType: 'json',
            data: transaction,
            success: function (data) {
                console.log(data);
                updateBalance(amount);
            },
            error: function (error) {
                console.error(error);
            }
        });
    }

    function getUpdatedPrices() {
        // Realizar una llamada AJAX al servidor para obtener los precios actualizados desde la base de datos
        $.ajax({
            url: 'fetch_prices.php', // Ruta del script PHP que obtiene los precios actualizados desde la base de datos
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                var newPriceSAndP = response.sp500;
                var newPriceDowJones = response.dowJones;
                
                robotTrading(newPriceSAndP, newPriceDowJones);
            },
            error: function(error) {
                console.error(error);
            }
        });
    }
</script>

</body>
</html>
