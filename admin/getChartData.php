<?php
// Datos de conexión a la base de datos
$servername = 'localhost';  // Dirección del servidor de la base de datos
$username = 'initbox';      // Nombre de usuario de la base de datos
$password = '12345678';     // Contraseña de la base de datos
$dbname = 'armentum';       // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);  // Si hay un error de conexión, terminar el script y mostrar el mensaje de error
}

// Obtener los parámetros de la solicitud AJAX
$tableName = $_GET['tableName'];  // Nombre de la tabla proporcionado en la solicitud AJAX
$field1 = $_GET['field1'];        // Primer campo proporcionado en la solicitud AJAX
$field2 = $_GET['field2'];        // Segundo campo proporcionado en la solicitud AJAX

// Consulta para obtener los datos de la tabla con los campos especificados
$sql = "SELECT $field1, $field2 FROM $tableName";  // Consulta SQL para seleccionar los campos específicos de la tabla especificada
$result = $conn->query($sql);  // Ejecutar la consulta

// Array para almacenar los resultados de la consulta
$data = array();

// Verificar si hay filas devueltas por la consulta
if ($result->num_rows > 0) {
    // Recorrer cada fila de resultados
    while ($row = $result->fetch_assoc()) {
        // Agregar cada fila de resultados al array de datos
        $data[] = $row;
    }
}

// Cerrar la conexión a la base de datos
$conn->close();

// Devolver los datos en formato JSON
header('Content-Type: application/json');  // Establecer la cabecera HTTP para indicar que el contenido es JSON
echo json_encode($data);  // Convertir el array de datos a formato JSON y mostrarlo como respuesta
?>
