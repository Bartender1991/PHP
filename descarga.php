<?php
// Configuración
$directorioDestino = "imagenes_descargadas"; // Directorio donde se guardarán las imágenes
$jsonFile = "links.json"; // Archivo JSON que contiene las URLs

// Verificar si el directorio existe, si no, crearlo
if (!file_exists($directorioDestino)) {
    mkdir($directorioDestino, 0777, true);
}

// Leer el archivo JSON
$contenidoJson = file_get_contents($jsonFile);
$urls = json_decode($contenidoJson, true);

// Verificar si se pudo leer el archivo JSON
if (!$urls) {
    die("Error al leer el archivo JSON.");
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
function descargarImagen($url, $directorioDestino)
{
    // Obtener el contenido de la imagen
    $imagen = file_get_contents($url);
    if ($imagen === false) {
        echo "Error al descargar la imagen: $url\n";
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
    echo "Imagen descargada: $nombreArchivoUnico\n";
}

// Descargar todas las imágenes
foreach ($urls as $url) {
    descargarImagen($url, $directorioDestino);
}

echo "¡Todas las imágenes han sido descargadas!\n";
?>
