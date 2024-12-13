<?php
// Mantener el script ejecutándose durante la descarga
set_time_limit(0);

// Verificar si los datos se enviaron por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ruta = $_POST['ruta'] ?? '';
    $contenido = $_POST['contenido'] ?? '';

    if (empty($ruta) || empty($contenido)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios.']);
        exit;
    }

    $urls = json_decode($contenido, true) ?: array_filter(array_map('trim', explode("\n", $contenido)));

    if (empty($urls)) {
        echo json_encode(['success' => false, 'message' => 'No se encontraron URLs válidas.']);
        exit;
    }

    if (!file_exists($ruta)) {
        mkdir($ruta, 0777, true);
    }

    $total = count($urls);
    $descargadas = 0;
    $inicio = microtime(true);

    foreach ($urls as $url) {
        $descargadas++;
        descargarImagen($url, $ruta);

        $tiempoTranscurrido = microtime(true) - $inicio;
        $estimadoRestante = $tiempoTranscurrido / $descargadas * ($total - $descargadas);

        echo json_encode([
            'success' => true,
            'progreso' => $descargadas,
            'total' => $total,
            'tiempoTranscurrido' => round($tiempoTranscurrido, 2),
            'estimadoRestante' => round($estimadoRestante, 2)
        ]);

        flush();
        ob_flush();

        usleep(500000);
    }

    echo json_encode(['success' => true, 'message' => 'Descarga completa.']);
    flush();
    ob_flush();
    exit;
}

/**
 * Descargar una imagen desde una URL y guardarla en un directorio especificado.
 * Si el archivo ya existe, se le agrega un número incremental al nombre antes de la extensión.
 *
 * @param string $url URL de la imagen a descargar
 * @param string $directorioDestino Ruta del directorio donde se guardará la imagen
 */
function descargarImagen($url, $directorioDestino)
{
    $imagen = @file_get_contents($url);
    if ($imagen === false) return;

    $nombreArchivo = basename($url);
    $rutaCompleta = $directorioDestino . DIRECTORY_SEPARATOR . $nombreArchivo;

    // Si el archivo ya existe, generar un nuevo nombre con un número incremental
    $rutaCompleta = generarNombreUnico($rutaCompleta);

    file_put_contents($rutaCompleta, $imagen);
}

/**
 * Genera un nombre de archivo único agregando un número incremental si ya existe.
 *
 * @param string $rutaCompleta Ruta completa del archivo (incluyendo nombre y extensión)
 * @return string Ruta única del archivo
 */
function generarNombreUnico($rutaCompleta)
{
    $directorio = dirname($rutaCompleta);
    $nombreArchivo = pathinfo($rutaCompleta, PATHINFO_FILENAME);
    $extension = pathinfo($rutaCompleta, PATHINFO_EXTENSION);

    $contador = 1;
    while (file_exists($rutaCompleta)) {
        $rutaCompleta = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo . " ($contador)." . $extension;
        $contador++;
    }

    return $rutaCompleta;
}