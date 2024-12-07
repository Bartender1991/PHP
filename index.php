<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Rutas</title>
    <style>
        /* Estilos básicos para el formulario y los botones */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"], textarea {
            width: 100%; /* Hacer que los campos ocupen todo el ancho disponible */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc; /* Borde gris claro */
            border-radius: 5px; /* Bordes redondeados */
        }
        button {
            padding: 10px 15px; /* Espaciado interno */
            background-color: #007BFF; /* Azul */
            color: white; /* Texto en blanco */
            border: none; /* Sin borde */
            border-radius: 5px; /* Bordes redondeados */
            cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
        }
        button:hover {
            background-color: #0056b3; /* Azul más oscuro al pasar el ratón */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestor de Rutas</h1>
        <form action="descarga.php" method="post">
            <!-- Campo de texto para mostrar la ruta seleccionada -->
            <label for="ruta">Ruta seleccionada:</label>
            <input type="text" id="ruta" name="ruta" readonly placeholder="Seleccione una carpeta">

            <!-- Botón para abrir el navegador de carpetas -->
            <button type="button" id="seleccionarCarpeta">Seleccionar Carpeta</button>

            <!-- Área de texto para contenido -->
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="6" placeholder="Ingrese el contenido aquí..." required></textarea>

            <!-- Botón de enviar formulario -->
            <button type="submit">Enviar</button>
        </form>
    </div>

    <script>
        // Asigna un evento al botón "Seleccionar Carpeta"
        document.getElementById('seleccionarCarpeta').addEventListener('click', () => {
            // Hace una solicitud al script PHP que abre el navegador de carpetas
            fetch('abrirNavegador.php')
                .then(response => response.json()) // Convierte la respuesta a JSON
                .then(data => {
                    if (data.success) {
                        // Si la respuesta es exitosa, muestra la ruta en el campo de texto
                        document.getElementById('ruta').value = data.path;
                        alert('Ruta seleccionada: ' + data.path);
                    } else {
                        // Si hubo un error, muestra un mensaje
                        alert('No se pudo seleccionar la carpeta.');
                    }
                })
                .catch(error => {
                    // Manejo de errores en caso de fallo en la solicitud
                    console.error('Error:', error);
                    alert('Hubo un error al seleccionar la carpeta.');
                });
        });
    </script>
</body>
</html>
