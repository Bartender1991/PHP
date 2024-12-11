<?php
// Mantener el script ejecutándose durante la descarga
set_time_limit(0);

// Verificar si los datos se enviaron por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados por el cliente
    $ruta = $_POST['ruta'] ?? ''; // Ruta de destino para las descargas
    $contenido = $_POST['contenido'] ?? ''; // Lista de URLs a descargar

    // Validar que se hayan proporcionado datos válidos
    if (empty($ruta) || empty($contenido)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios.']);
        exit; // Finalizar ejecución si faltan datos
    }

    // Procesar las URLs recibidas
    $urls = json_decode($contenido, true) ?: array_filter(array_map('trim', explode("\n", $contenido)));

    // Verificar si la lista de URLs es válida y contiene al menos una URL
    if (empty($urls)) {
        echo json_encode(['success' => false, 'message' => 'No se encontraron URLs válidas.']);
        exit; // Finalizar ejecución si no hay URLs válidas
    }

    // Crear la carpeta de destino si no existe
    if (!file_exists($ruta)) {
        mkdir($ruta, 0777, true); // Crear carpeta con permisos adecuados
    }

    // Variables para el progreso
    $total = count($urls); // Total de URLs a descargar
    $descargadas = 0; // Contador de descargas realizadas
    $inicio = microtime(true); // Tiempo de inicio para medir el progreso

    // Procesar cada URL
    foreach ($urls as $url) {
        $descargadas++; // Incrementar el contador de descargas
        descargarImagen($url, $ruta); // Descargar la imagen

        // Calcular el tiempo transcurrido y estimado restante
        $tiempoTranscurrido = microtime(true) - $inicio;
        $estimadoRestante = $tiempoTranscurrido / $descargadas * ($total - $descargadas);

        // Enviar el progreso al cliente
        echo json_encode([
            'success' => true,
            'progreso' => $descargadas,
            'total' => $total,
            'tiempoTranscurrido' => round($tiempoTranscurrido, 2), // Redondear a 2 decimales
            'estimadoRestante' => round($estimadoRestante, 2) // Redondear a 2 decimales
        ]);

        // Enviar la salida al cliente de inmediato
        flush();
        ob_flush();

        // Simular un retardo (puedes eliminar esta línea en producción)
        usleep(500000); // Pausar 0.5 segundos para pruebas
    }

    // Enviar mensaje de finalización
    echo json_encode(['success' => true, 'message' => 'Descarga completa.']);
    exit; // Finalizar la ejecución
}

/**
 * Descargar una imagen desde una URL y guardarla en un directorio especificado.
 * 
 * @param string $url URL de la imagen a descargar
 * @param string $directorioDestino Ruta del directorio donde se guardará la imagen
 */
function descargarImagen($url, $directorioDestino) {
    $imagen = @file_get_contents($url); // Obtener el contenido de la imagen desde la URL
    if ($imagen === false) return; // Si falla, no hacer nada

    $nombreArchivo = basename($url); // Obtener el nombre del archivo desde la URL
    $rutaCompleta = $directorioDestino . DIRECTORY_SEPARATOR . $nombreArchivo; // Ruta completa del archivo

    file_put_contents($rutaCompleta, $imagen); // Guardar la imagen en el directorio
}
?>
