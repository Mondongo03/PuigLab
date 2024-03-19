<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablas con dos campos y Gráficos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Incluye la biblioteca Chart.js -->
</head>
<body>
    <h1>Tablas con dos campos y Gráficos</h1>

    <div id="tableContainer">
        <table border='1'>
            <tr>
                <th>Nombre de la Tabla</th>
                <th>Nombre del Campo 1</th>
                <th>Nombre del Campo 2</th>
                <th>Generar Gráfico</th>
            </tr>
            <?php
            // Datos de conexión a la base de datos
            $servername = 'localhost';
            $username = 'initbox';
            $password = '12345678';
            $dbname = 'armentum';

            // Crear conexión
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Comprobar la conexión
            if ($conn->connect_error) {
                die("Error de conexión: " . $conn->connect_error);
            }

            $tables = array();

            // Consulta para obtener los nombres de las tablas en la base de datos
            $sql = "SHOW TABLES";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_row()) {
                    $tableName = $row[0];

                    // Consulta para obtener los nombres de los campos de la tabla actual
                    $fieldNames = array();
                    $fieldSql = "SHOW COLUMNS FROM $tableName";
                    $fieldResult = $conn->query($fieldSql);
                    while ($fieldRow = $fieldResult->fetch_assoc()) {
                        $fieldNames[] = $fieldRow['Field'];
                    }

                    // Agregar los datos de la tabla actual al arreglo de tablas
                    $tables[] = array(
                        'name' => $tableName,
                        'fields' => $fieldNames
                    );
                }
            }

            $conn->close();

            // Mostrar las tablas y botones para generar gráficos
            foreach ($tables as $table) {
                echo "<tr>";
                echo "<td>{$table['name']}</td>"; // Muestra el nombre de la tabla
                echo "<td>{$table['fields'][0]}</td>"; // Muestra el primer campo de la tabla
                echo "<td>{$table['fields'][1]}</td>"; // Muestra el segundo campo de la tabla
                echo "<td><button onclick='generateChart(\"{$table['name']}\", \"{$table['fields'][0]}\", \"{$table['fields'][1]}\")'>Generar Gráfico</button></td>"; // Botón para generar el gráfico
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <canvas id="myChart" width="400" height="400"></canvas> <!-- Lienzo para el gráfico -->

    <script>
        var myChart = null;

        function generateChart(tableName, field1, field2) {
            // Eliminar el gráfico anterior si existe
            if (myChart) {
                myChart.destroy();
            }

            // Consulta AJAX para obtener los datos de la tabla con el nombre proporcionado
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var responseData = JSON.parse(xhr.responseText);
                    var labels = [];
                    var data = [];

                    // Extraer los nombres de los campos y los datos de la respuesta
                    responseData.forEach(function(item) {
                        labels.push(item[field1]);
                        data.push(item[field2]);
                    });

                    // Crear el gráfico con los datos obtenidos
                    var ctx = document.getElementById('myChart').getContext('2d');
                    myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels, // Etiquetas del eje X
                            datasets: [{
                                label: tableName, // Nombre de la tabla como etiqueta del conjunto de datos
                                data: data, // Datos del eje Y
                                backgroundColor: 'rgba(255, 99, 132, 0.2)', // Color de fondo de las barras
                                borderColor: 'rgba(255, 99, 132, 1)', // Color del borde de las barras
                                borderWidth: 1 // Ancho del borde de las barras
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true // El eje Y comienza en cero
                                }
                            }
                        }
                    });
                }
            };
            xhr.open("GET", "getChartData.php?tableName=" + tableName + "&field1=" + field1 + "&field2=" + field2, true); // Solicitud AJAX para obtener los datos del gráfico
            xhr.send();
        }
    </script>
</body>
</html>
