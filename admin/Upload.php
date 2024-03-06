<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica si el archivo fue cargado sin errores
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
        $filename = $_FILES["fileToUpload"]["name"];
        $filetype = $_FILES["fileToUpload"]["type"];
        $filesize = $_FILES["fileToUpload"]["size"];

        // Verifica la extensión del archivo
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext != "csv") {
            die("Por favor, sube solo archivos .csv.");
        }

        // Mueve el archivo a la carpeta de destino
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $filename);

        // Abre el archivo CSV
        $file = fopen($filename, 'r');

        // Lee la primera línea del archivo CSV
        $cabecera = fgetcsv($file);
        $primeraLinea = fgetcsv($file);
        $arrayBoolean = array_fill(0, count($cabecera), false);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Imprime valores PHP en script JavaScript
            echo "<script>";
            echo "var cabeceras = " . json_encode($cabecera) . ";";
            echo "var arrayBoolean = Array(cabeceras.length).fill(false);";

            echo "function comprobarCheckbox() {";
            echo "var resultado = document.getElementById('resultado');";
            echo "resultado.innerHTML = '';";

            echo "for (var i = 0; i < cabeceras.length; i++) {";
            echo "  var checkboxValue = document.getElementById(cabeceras[i]).checked;";
            echo "  arrayBoolean[i] = checkboxValue;";
            echo "  if (checkboxValue) {";
            echo "    resultado.innerHTML += 'La checkbox ' + cabeceras[i] + ' está marcada. Valor específico<br>';";
            echo "  } else {";
            echo "    resultado.innerHTML += 'La checkbox ' + cabeceras[i] + ' no está marcada. Valor específico<br>';";
            echo "  }";
            echo "}";
            echo "document.getElementById('arrayPrint').innerHTML = 'Array de JavaScript: ' + JSON.stringify(arrayBoolean);";
            echo "}";

            echo "function generarNuevoCSV() {";
            echo "var nuevoCSV = '';";

            // Cerrar y abrir el archivo CSV de nuevo
            fclose($file);
            $file = fopen($filename, 'r');

            // Regresar al inicio del archivo CSV
            rewind($file);
            $fila = 0;
            while (($line = fgetcsv($file, 1000, ",")) !== false) {
                $numero = count($line);
                $fila++;
                for ($c = 0; $c < $numero; $c++) {
                    echo "if (arrayBoolean[" . $c . "] == true) {";
                    if($c != 0){
                        echo "nuevoCSV += ',';";
                    }
                    echo "nuevoCSV += '" . $line[$c] . "'; ";
                    echo "}";
                }
                echo "nuevoCSV += '\\n';";
            }
            echo "var contenidoArchivo = nuevoCSV;";
            echo "var xhttp = new XMLHttpRequest();";
            echo "xhttp.onreadystatechange = function() {";
            echo "    if (this.readyState == 4 && this.status == 200) {";
            echo "        console.log(this.responseText);";
            echo "    }";
            echo "};";
            echo "xhttp.open('POST', 'descargaCSV.php', true);";
            echo "xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');";
            echo "xhttp.send('contenido=' + encodeURIComponent(contenidoArchivo));";
            echo "}";

            echo "</script>";
        } else {
            echo "Error: Método de solicitud incorrecto.";
        }

        // Imprime la tabla HTML con scroll horizontal
        echo "<div style='overflow-x: auto;'>";
        echo "<table style='border-collapse: collapse; border: 1px solid black; table-layout: fixed; width: 100%;'>";

        echo "<tr>";
        echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>Campo</td>";
        echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>Tipo</td>";
        // Agrega una columna con checkbox
        echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>Seleccionar</td>";
        echo "</tr>";

        $i = 0;
        // Imprime las filas del archivo CSV en la primera columna
        foreach ($cabecera as $campo) {
            // Agrega una columna con el campo
            echo "<tr>";
            echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>{$campo}</td>";

            // Agrega una columna con el tipo
            if (is_numeric($primeraLinea[$i]) && (float)$primeraLinea[$i] == $primeraLinea[$i]) {
                echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>Float</td>";
            }
            // Intenta convertir a booleano
            elseif ($primeraLinea[$i] === 'true' || $primeraLinea[$i] === 'false') {
                echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>Boolean</td>";
            }
            // Si no se puede convertir a entero, float ni booleano, se asume string
            else {
                echo "<td style='border: 1px solid black; padding: 5px; text-align: center'>String</td>";
            }

            // Agrega una columna con checkbox
            echo "<td style='border: 1px solid black; padding: 5px; text-align: center'><input type='checkbox' name='seleccionar[]' value='{$line[0]}' id='{$cabecera[$i]}'></td>";
            echo "</tr>";
            $i++;
        }

        echo "</table>";
        echo "</div>";
        rewind($file);

        echo "<pre>";
        echo "</pre>";
        echo "<button onclick='comprobarCheckbox(); generarNuevoCSV();'>Comprobar Checkbox</button>";

        echo "<p id='resultado'></p>";
        echo "<p id='arrayPrint'></p>";
        // Cierra el
        fclose($file);
    } else {
        echo "Hubo un error al subir tu archivo.";
    }
} else {
    echo "Error: Método de solicitud incorrecto.";
}
?>
