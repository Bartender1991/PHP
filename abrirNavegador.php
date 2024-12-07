<?php
if (PHP_OS_FAMILY === 'Windows') {
    // Ejecuta PowerShell para abrir el navegador de carpetas
    $output = shell_exec(
        'powershell.exe -NoProfile -Command "[System.Reflection.Assembly]::LoadWithPartialName(\'System.Windows.Forms\') | Out-Null; $folderBrowser = New-Object System.Windows.Forms.FolderBrowserDialog; $folderBrowser.ShowDialog() | Out-Null; $folderBrowser.SelectedPath"'
    );
    
    // Limpia la salida para obtener solo la ruta seleccionada
    $path = trim($output);

    if (!empty($path)) {
        echo json_encode(['success' => true, 'path' => $path]);
    } else {
        echo json_encode(['success' => false, 'path' => '', 'message' => 'No se seleccionó ninguna carpeta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Esta función solo es compatible con Windows.']);
}
?>
