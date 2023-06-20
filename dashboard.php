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

    // Obtener el historial de transacciones del usuario
    $sqlTransacciones = "SELECT * FROM transacciones WHERE usuario_id = $usuarioId ORDER BY fecha DESC";
    $resultTransacciones = $conn->query($sqlTransacciones);
    $transacciones = $resultTransacciones->fetch_all(MYSQLI_ASSOC);

    $conn->close();
    ?>

    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                <h4 class=" text-white">Saldo: $<span id="balance">0.00</span></h4>
                <button class="btn btn-success" id="startButton">Iniciar Trading</button>
                <button class="btn btn-danger" id="stopButton" disabled>Detener Trading</button>
            </div>
            </div>
        </div>
    </div>
    <h3>Historial de Transacciones</h3>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Descripción</th>
            <th>Monto</th>
            <th>Fecha</th>
        </tr>
        </thead>
        <tbody id="transactionHistory">
        <?php foreach ($transacciones as $transaccion): ?>
            <tr>
            <td><?php echo $transaccion['descripcion']; ?></td>
            <td><?php echo $transaccion['monto']; ?></td>
            <td><?php echo $transaccion['fecha']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
   

    <script>
        
        // Variables globales
    var saldo = 0;
    var acciones = 0;
    function buyStocks(price) {
        // Lógica de compra de acciones
    }

    function sellStocks(price) {
        // Lógica de venta de acciones
    }

    document.addEventListener('DOMContentLoaded', function() {
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

        document.getElementById('transactionHistory').appendChild(row);
        }

        function simulateTrading() {
    // Obtener los precios actuales de las acciones
    var sp500Price = stockData.datasets[0].data[stockData.datasets[0].data.length - 1];
    var dowJonesPrice = stockData.datasets[1].data[stockData.datasets[1].data.length - 1];

    for (var i = 0; i < getRandomNumberInRange(3, 4); i++) {
        if (sp500Price < 3000) {
            buyStocks(sp500Price);
        } else if (sp500Price > 3500) {
            sellStocks(sp500Price);
        }
    }

    fetch('simulate_trading.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                // Actualizar la tabla de transacciones
                fetchTransactionHistory();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error(error);
            alert('Ocurrió un error al simular el trading.');
        });
}
function getRandomNumberInRange(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}


        function fetchTransactionHistory() {
        fetch('fetch_transactions.php')
            .then(response => response.json())
            .then(data => {
            // Limpiar la tabla de transacciones
            const tableBody = document.getElementById('transactionHistory');
            tableBody.innerHTML = '';

            // Agregar las transacciones al cuerpo de la tabla
            data.forEach(transaction => {
                const row = document.createElement('tr');

                const descriptionCell = document.createElement('td');
                descriptionCell.textContent = transaction.descripcion;
                row.appendChild(descriptionCell);

                const amountCell = document.createElement('td');
                amountCell.textContent = transaction.monto;
                row.appendChild(amountCell);

                const dateCell = document.createElement('td');
                dateCell.textContent = transaction.fecha;
                row.appendChild(dateCell);

                tableBody.appendChild(row);
            });
            })
            .catch(error => {
            console.error(error);
            alert('Ocurrió un error al obtener el historial de transacciones.');
            });
        }

        function initializeBalance() {
        var lastTransaction = <?php echo json_encode(end($transacciones)); ?>;
        var initialBalance = 50000;

        if (lastTransaction !== null) {
            initialBalance = parseFloat(lastTransaction.monto);
        }

        document.getElementById('balance').textContent = initialBalance.toFixed(1);

        if (initialBalance > 0) {
            document.getElementById('startButton').disabled = false;
        } else {
            console.log('No hay saldo disponible para iniciar la simulación.');
        }
        }

        initializeBalance();
        fetchData(); // Obtener los datos iniciales del gráfico desde la base de datos

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
            success: function(data) {
            console.log(data);
            },
            error: function(error) {
            console.error(error);
            }
        });
        }

        var intervalId = null;

        function startTrading() {
        intervalId = setInterval(generateRandomData, 1000);
        simulateTrading();

        document.getElementById('startButton').disabled = true;
        document.getElementById('stopButton').disabled = false;
        }

        function stopTrading() {
        clearInterval(intervalId);
        intervalId = null;

        document.getElementById('startButton').disabled = false;
        document.getElementById('stopButton').disabled = true;
        }

        document.getElementById('startButton').addEventListener('click', startTrading);
        document.getElementById('stopButton').addEventListener('click', stopTrading);

        setInterval(fetchData, 5000); // Actualizar los datos del gráfico cada 5 segundos
    });

    function getRandomNumberInRange(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    </script>
    </body>
    </html>
