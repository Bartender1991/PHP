// Función que realiza la solicitud fetch y actualiza el campo de texto
function seleccionarCarpeta(boton, campoTexto) {
    boton.addEventListener('click', () => {
        //mostrarMensaje(campoTexto.value);

        // Obtenemos el valor del campo de texto
        const rutaActual = campoTexto.value;

        // Realiza la solicitud al script PHP
        fetch('abrirNavegador.php', {
            method: 'POST', // Usamos POST para enviar datos
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', // Indicamos el tipo de contenido
            },
            body: new URLSearchParams({
                ruta: rutaActual, // Enviamos el valor del campo de texto como parámetro
            })
        })
            .then(response => response.json()) // Convierte la respuesta a JSON
            .then(data => {
                if (data.success) {
                    // Actualiza el valor del campo de texto con la ruta seleccionada
                    campoTexto.value = data.path;
                    mostrarMensaje('Ruta seleccionada: ' + data.path);
                } else {
                    // Mensaje si el usuario cancela o hay un error en PHP
                    mostrarMensaje(data.message || 'No se pudo seleccionar la carpeta.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Hubo un error inesperado al seleccionar la carpeta.');
            });
    });
}


// Función que asigna la funcionalidad de limpiar el área de texto
function limpiarContenido(boton, textarea) {
    boton.addEventListener('click', (event) => {
        event.preventDefault(); // Prevenir el comportamiento predeterminado
        textarea.value = ""; // Limpiar el contenido del área de texto
        mostrarMensaje('Se limpio contenido');
    });
}

// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    mostrarMensaje('esto es un mensaje');
    // Obtiene las referencias del formulario
    const botonSeleccionar = document.getElementById('seleccionarCarpeta');
    const campoRuta = document.getElementById('ruta');
    const btnLimpiar = document.getElementById('Limpiar');
    const textareaContenido = document.getElementById('contenido');

    // Llama a la función para asignar la funcionalidad al botón
    seleccionarCarpeta(botonSeleccionar, campoRuta);

    // Llama a la función para asignar la funcionalidad de limpiar
    limpiarContenido(btnLimpiar, textareaContenido);
});

// Función para mostrar un mensaje
function mostrarMensaje(texto, tipo = 'info', duracion = 3000) {
    const container = document.getElementById('messageContainer');

    // Crear un nuevo elemento para el mensaje
    const mensaje = document.createElement('div');
    mensaje.className = `message ${tipo}`;
    mensaje.textContent = texto;

    // Agregar el mensaje al contenedor
    container.appendChild(mensaje);

    // Mostrar el mensaje con la clase 'show'
    setTimeout(() => mensaje.classList.add('show'), 100);

    // Ocultar y eliminar el mensaje después de la duración especificada
    setTimeout(() => {
        mensaje.classList.add('hide');
        mensaje.addEventListener('transitionend', () => mensaje.remove());
    }, duracion);
}

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
    const rruta = document.getElementById('idruta'); // Modal de progreso
    const modal = document.getElementById('modalProgreso'); // Modal de progreso
    const progresoTexto = document.getElementById('progresoTexto'); // Elemento para mostrar progreso
    const tiempoTexto = document.getElementById('tiempoTexto'); // Elemento para mostrar tiempo
    const cancelarDescarga = document.getElementById('cancelarDescarga'); // Botón de cancelar

    let cancelado = false; // Bandera para controlar si la descarga fue cancelada

    // Función para limpiar los campos del modal
    function limpiarModal() {
        rruta.textContent = "";
        progresoTexto.textContent = "Preparando descargas...";
        tiempoTexto.textContent = "";
    }

    // Mostrar el modal al iniciar el proceso
    modal.style.display = 'block';

    // Agregar evento para manejar la cancelación
    cancelarDescarga.addEventListener('click', () => {
        cancelado = true; // Actualizar la bandera
        mostrarMensaje('Descarga cancelada.'); // Mostrar mensaje al usuario
        modal.style.display = 'none'; // Ocultar el modal
        limpiarModal(); // Limpiar los datos del modal
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
                        limpiarModal(); // Limpiar los datos del modal
                        mostrarMensaje('Proceso finalizado.');
                        return;
                    }

                    // Decodificar el fragmento recibido
                    const texto = decoder.decode(value);
                    console.log('texto recibido:', texto);
                    try {
                        // Intentar analizar los datos como JSON
                        const data = JSON.parse(texto);
                        if (data.success) {
                            // Actualizar progreso y tiempos en el modal
                            rruta.textContent = ruta;
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
            mostrarMensaje('Hubo un error al enviar el formulario.');
            modal.style.display = 'none'; // Ocultar el modal en caso de error
            limpiarModal(); // Limpiar los datos del modal
        });
});