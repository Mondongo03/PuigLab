<?php
// Verifica que se haya enviado el contenido del archivo
if (isset($_POST['contenido'])) {
    // Obtén el contenido del archivo desde la solicitud POST
    $contenidoArchivo = $_POST['contenido'];

    // Define la ruta del archivo en el servidor
    $rutaArchivo = '/var/www/admin/filtrado.csv';

    // Guarda el contenido en el archivo en el servidor
    file_put_contents($rutaArchivo, $contenidoArchivo);

    // Envía una respuesta al cliente (puedes personalizar este mensaje)
    echo "Archivo guardado con éxito en el servidor.";
} else {
    // Envía un mensaje de error si no se proporciona contenido
    echo "Error: No se proporcionó contenido del archivo.";
}
?>
