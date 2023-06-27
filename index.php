<?php
date_default_timezone_set('America/Lima');

?>


<!DOCTYPE html>
<html>
<head>
    <script src="chart.js"></script>
    <script src="jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="bootstrap.min.css">

</head>
<body>
  <div class="container-fluid ">
    <h1 class="ttitulo  text-center text-dark">BIENBENIDO A LA BOLSA DE VALORES </h1>

    <a href="login.php" class="btn btn-primary  ">Iniciar seción o registrarse</a>
  </div>

  <div class="fluid-container m-7">
    <canvas id="stockChart"></canvas>
  </div>
  <script>
      // Realizar truncado de tabla al cerrar la página
    window.addEventListener('beforeunload', function() {
      fetch('truncate_table.php')
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error(error));
    }); 
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

      setInterval(generateRandomData, 1000);
      setInterval(fetchData, 5000); // Actualizar los datos del gráfico cada 3 segundos
    });

    function getRandomNumberInRange(min, max) {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    }
  </script>
</body>
</html>
