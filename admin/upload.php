<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica si el archivo fue cargado sin errores
    if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0){
        $filename = $_FILES["fileToUpload"]["name"];
        $filetype = $_FILES["fileToUpload"]["type"];
        $filesize = $_FILES["fileToUpload"]["size"];
    
        // Verifica la extensión del archivo
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if($ext != "csv") {
            die("Por favor, sube solo archivos .csv.");
        }
    
        // Mueve el archivo a la carpeta de destino
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename);
    
        // Abre el archivo CSV
        $file = fopen($filename, 'r');

        // Lee la cabecera del archivo CSV
        $cabecera = fgetcsv($file);

        // Imprime la tabla HTML
        echo "<table style='border-collapse: collapse; margin: auto; border: 1px solid black; table-layout: fixed; width: 100%;'>";
        
        // Imprime la cabecera de la tabla
        echo "<tr>";
        foreach ($cabecera as $field) {
            echo "<td style='border: 1px solid black; padding: 5px; text-align: center;'><strong>{$field}</strong></td>";
        }
        echo "</tr>";

        // Lee una fila del archivo CSV para obtener los tipos de variables
        $line = fgetcsv($file);

        // Imprime la fila con los tipos de variables
        echo "<tr>";
        foreach ($line as $field) {
            // Intenta convertir a entero
            if (is_numeric($field) && (int)$field == $field) {
                echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>int</td>";
            }
            // Intenta convertir a punto flotante
            elseif (is_numeric($field) && (float)$field == $field) {
                echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>float</td>";
            }
            // Intenta convertir a booleano
            elseif ($field === 'true' || $field === 'false') {
                echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>boolean</td>";
            }
            // Si no se puede convertir a entero, float ni booleano, se asume string
            else {
                echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>string</td>";
            }
        }
        echo "</tr>";

        echo "</table>";

        // Cierra el archivo CSV
        fclose($file);        
    } else {
        echo "Hubo un error al subir tu archivo.";
    }
} else {
    echo "Error: Método de solicitud incorrecto.";
}
?>
