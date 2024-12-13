<?php
if (PHP_OS_FAMILY === 'Windows') {
    // Ejecuta PowerShell para abrir el navegador de carpetas
     // Verificar si se recibió una ruta válida desde el frontend
     $rutaInicial = isset($_POST['ruta']) && !empty($_POST['ruta']) ? $_POST['ruta'] : 'Desktop';


    $output = shell_exec(
        //'powershell.exe -NoProfile -Command "[System.Reflection.Assembly]::LoadWithPartialName(\'System.Windows.Forms\') | Out-Null; $folderBrowser = New-Object System.Windows.Forms.FolderBrowserDialog; $folderBrowser.ShowDialog() | Out-Null; $folderBrowser.SelectedPath"'
        'powershell.exe -NoProfile -Command "Add-Type -AssemblyName PresentationFramework; $dialog = New-Object Microsoft.Win32.OpenFileDialog; $dialog.InitialDirectory = [Environment]::GetFolderPath(\''.$rutaInicial.'\'); $dialog.CheckFileExists = $false; $dialog.CheckPathExists = $true; $dialog.ValidateNames = $false; $dialog.FileName = \'Selecciona una carpeta\'; if ($dialog.ShowDialog()) { Split-Path $dialog.FileName }"'
    );

    // Limpia la salida para obtener solo la ruta seleccionada
    $path = trim($output);
    // Verifica si el usuario canceló el cuadro de diálogo
    if ($path === 'CANCEL') {
        echo json_encode(['success' => false, 'path' => '', 'message' => 'se cancelo No se seleccionó ninguna carpeta.']);
    } elseif (!empty($path)) {
        echo json_encode(['success' => true, 'path' => $path]);
    } else {
        echo json_encode(['success' => false, 'path' => '', 'message' => 'Error inesperado al seleccionar la carpeta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Esta función solo es compatible con Windows.']);
}
