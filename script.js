// Función que realiza la solicitud fetch y actualiza el campo de texto
function seleccionarCarpeta(boton, campoTexto) {
    boton.addEventListener('click', () => {
        // Realiza la solicitud al script PHP
        fetch('abrirNavegador.php')
            .then(response => response.json()) // Convierte la respuesta a JSON
            .then(data => {
                if (data.success) {
                    // Actualiza el valor del campo de texto con la ruta seleccionada
                    campoTexto.value = data.path;
                    alert('Ruta seleccionada: ' + data.path);
                } else {
                    // Mensaje si el usuario cancela o hay un error en PHP
                    alert(data.message || 'No se pudo seleccionar la carpeta.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error inesperado al seleccionar la carpeta.');
            });
    });
}

// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    // Obtiene las referencias del formulario
    const botonSeleccionar = document.getElementById('seleccionarCarpeta');
    const campoRuta = document.getElementById('ruta');

    // Llama a la función para asignar la funcionalidad al botón
    seleccionarCarpeta(botonSeleccionar, campoRuta);
});

document.getElementById('formRuta').addEventListener('submit', function (event) {
    event.preventDefault(); // Evitar que el formulario se envíe de forma tradicional

    // Obtener los datos del formulario
    const ruta = document.getElementById('ruta').value; // Ruta seleccionada por el usuario
    const contenido = document.getElementById('contenido').value; // URLs ingresadas en el textarea

    // Crear un objeto FormData para enviar los datos al servidor
    const formData = new FormData();
    formData.append('ruta', ruta);
    formData.append('contenido', contenido);

    // Referencias al modal y sus elementos
    const modal = document.getElementById('modalProgreso'); // Modal de progreso
    const progresoTexto = document.getElementById('progresoTexto'); // Elemento para mostrar progreso
    const tiempoTexto = document.getElementById('tiempoTexto'); // Elemento para mostrar tiempo
    const cancelarDescarga = document.getElementById('cancelarDescarga'); // Botón de cancelar

    let cancelado = false; // Bandera para controlar si la descarga fue cancelada

    // Mostrar el modal al iniciar el proceso
    modal.style.display = 'block';

    // Agregar evento para manejar la cancelación
    cancelarDescarga.addEventListener('click', () => {
        cancelado = true; // Actualizar la bandera
        alert('Descarga cancelada.'); // Mostrar mensaje al usuario
        modal.style.display = 'none'; // Ocultar el modal
    });

    // Enviar los datos al servidor usando fetch
    fetch('descarga.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            // Leer la respuesta del servidor como un stream
            const reader = response.body.getReader(); // Obtener lector de streams
            const decoder = new TextDecoder(); // Decodificador de texto

            // Función para leer datos en fragmentos
            function readChunk() {
                if (cancelado) return; // Salir si la descarga fue cancelada

                reader.read().then(({ done, value }) => {
                    if (done) {
                        // Cuando el proceso termina, ocultar el modal y notificar al usuario
                        modal.style.display = 'none';
                        alert('Proceso finalizado.');
                        return;
                    }

                    // Decodificar el fragmento recibido
                    const texto = decoder.decode(value);
                    try {
                        // Intentar analizar los datos como JSON
                        const data = JSON.parse(texto);
                        if (data.success) {
                            // Actualizar progreso y tiempos en el modal
                            progresoTexto.textContent = `Descargadas: ${data.progreso} de ${data.total}`;
                            tiempoTexto.textContent = `Tiempo transcurrido: ${data.tiempoTranscurrido}s, estimado restante: ${data.estimadoRestante}s`;
                        }
                    } catch (e) {
                        // Manejar errores en el procesamiento de datos
                        console.error('Error al procesar el progreso:', e);
                    }

                    // Continuar leyendo el siguiente fragmento
                    readChunk();
                });
            }

            // Iniciar la lectura de datos
            readChunk();
        })
        .catch(error => {
            // Manejar errores en la solicitud
            console.error('Error:', error);
            alert('Hubo un error al enviar el formulario.');
            modal.style.display = 'none'; // Ocultar el modal en caso de error
        });
});
