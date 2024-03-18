<?php
    //Recoger el String anterior
    if (isset($_POST['contenido'])) {
        // Obtén el contenido del archivo desde la solicitud POST
        $contenidoArchivo = $_POST['contenido'];
    }
    // Conexion a la base de datos
    $servername = "localhost";
    $username = "initbox";
    $password = "12345678";
    $dbname = "armentum";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    //Contar cuantas tablas hay
    $result = $conn->query("SHOW TABLES LIKE 'datos%'");
    $lastTable = $result->num_rows;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tableName = $row['Tables_in_basedatos'];
            // Extraer el número de la tabla
            $tableNumber = intval(substr($tableName, 5));
            if ($tableNumber > $lastTable) {
                $lastTable = $tableNumber;
            }
        }
    }
    $newTableName = 'datos' . ($lastTable + 1);
    $lineas = explode("\n", $contenidoArchivo);
    $columnas = explode(",", $lineas[0]);
    //Creacion de la tabla
    $sql = "CREATE TABLE IF NOT EXISTS $newTableName (";
    foreach ($columnas as $columna) {
        if (is_numeric($columna)) {
            $sql .= "$columna INT, ";
        } else {
            $sql .= "$columna VARCHAR(255), ";
        }
    }
    $sql = rtrim($sql, ", ");
    $sql .= ")";
    //Añadir la tabla
    if ($conn->query($sql) === TRUE) {
    echo "Tabla credada exitosamente.<br>";
    // Insertar datos en la tabla
        for ($i = 1; $i < count($lineas); $i++) {
            $valores = explode(",", $lineas[$i]);
            $sqlInsert = "INSERT INTO $newTableName (".implode(",", $columnas).") VALUES ('".implode("','", $valores)."')";
            if ($conn->query($sqlInsert) === TRUE) {
                echo "Datos insertados correctamente.<br>";
            } else {
                echo "Error al insertar datos: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "Error al crear la tabla: " . $conn->error;
    }
    // Verifica que se haya enviado el contenido del archivo
    $conn->close();
?>
