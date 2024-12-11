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
                    alert('No se pudo seleccionar la carpeta.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al seleccionar la carpeta.');
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
    event.preventDefault();  // Prevenir el envío tradicional del formulario

    // Obtener los datos del formulario
    const ruta = document.getElementById('ruta').value;
    const contenido = document.getElementById('contenido').value;

    console.log(ruta,contenido);
    // Crear un objeto FormData para enviar los datos por POST
    const formData = new FormData();
    formData.append('ruta', ruta);
    formData.append('contenido', contenido);

    // Enviar los datos usando fetch
    fetch('descarga.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())  // Asumiendo que el servidor devuelve JSON
    .then(data => {
        // Puedes manejar la respuesta del servidor aquí
        console.log(data);
        alert('Formulario enviado exitosamente.');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error al enviar el formulario.');
    });
});
