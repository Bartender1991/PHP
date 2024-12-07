<?php
// Configuración inicial
// La ruta se tomará del formulario, por lo que no es necesario definirla aquí inicialmente
// El archivo 'links.json' debe estar presente con las URLs de las imágenes a descargar

// Verificar si los datos se enviaron por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer los datos enviados desde el formulario
    $ruta = $_POST['ruta'] ?? ''; // Ruta seleccionada
    $contenido = $_POST['contenido'] ?? ''; // Texto del textarea (URLs)

    // Validar que se haya proporcionado una ruta y contenido
    if (empty($ruta)) {
        die("Por favor, selecciona una carpeta.");
    }

    if (empty($contenido)) {
        die("Por favor, ingresa contenido en el textarea.");
    }

    // Verificar si el contenido es un JSON válido
    $urls = json_decode($contenido, true); // Intentamos convertir el contenido a JSON

    if (json_last_error() === JSON_ERROR_NONE) {
        // Si es un JSON válido, lo usamos directamente
        $urls = array_map('trim', $urls); // Limpiamos los valores del array
    } else {
        // Si no es un JSON válido, lo tratamos como texto
        $urls = array_filter(array_map('trim', explode("\n", $contenido)));
    }

    // Verificar que se hayan obtenido URLs válidas
    if (empty($urls)) {
        die("No se encontraron URLs válidas en el contenido.");
    }

    // Verificar si el directorio destino existe, si no, crearlo
    if (!file_exists($ruta)) {
        mkdir($ruta, 0777, true); // Crear el directorio con permisos
    }

    // Función para verificar si un archivo ya existe y generar un nombre único
    function obtenerNombreUnico($nombreArchivo, $directorioDestino) {
        $rutaCompleta = $directorioDestino . DIRECTORY_SEPARATOR . $nombreArchivo;
        $contador = 1;

        // Si el archivo ya existe, agregar un número al nombre
        while (file_exists($rutaCompleta)) {
            $nombreArchivoSinExt = pathinfo($nombreArchivo, PATHINFO_FILENAME);
            $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
            $nombreArchivo = $nombreArchivoSinExt . "_" . $contador . "." . $extension;
            $rutaCompleta = $directorioDestino . DIRECTORY_SEPARATOR . $nombreArchivo;
            $contador++;
        }

        return $nombreArchivo;
    }

    // Función para descargar y guardar la imagen
    function descargarImagen($url, $directorioDestino) {
        // Obtener el contenido de la imagen
        $imagen = @file_get_contents($url);
        if ($imagen === false) {
            echo "Error al descargar la imagen: $url<br>";
            return;
        }

        // Obtener el nombre de la imagen desde la URL
        $nombreArchivo = basename($url);

        // Obtener un nombre único para el archivo
        $nombreArchivoUnico = obtenerNombreUnico($nombreArchivo, $directorioDestino);

        // Ruta completa del archivo
        $rutaCompleta = $directorioDestino . DIRECTORY_SEPARATOR . $nombreArchivoUnico;

        // Guardar la imagen en el directorio
        file_put_contents($rutaCompleta, $imagen);
        echo "Imagen descargada: $nombreArchivoUnico<br>";
    }

    // Descargar todas las imágenes
    foreach ($urls as $url) {
        descargarImagen($url, $ruta);
    }

    echo "¡Todas las imágenes han sido descargadas!";
} else {
    // Si no se envió POST, mostrar un mensaje de error
    echo "No se recibieron datos del formulario.";
}
?>
