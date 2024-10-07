let estatDeLaPartida = {
    preguntes: [],
    contadorRespostas: 0,
    contadorPreguntes: 0,
    preguntaActual: 0,
    totalPreguntas: 10
};

function verificarNombre() {
    document.getElementById('numeroPreguntas').style.display = 'none';
    document.getElementById('juego').style.display = 'none';
    document.getElementById('resultadoFinal').style.display = 'none'; 
    document.getElementById('nombreUsuario').style.display = 'block'; 
    document.getElementById('guardarNombre').addEventListener('click', guardarNombre);
}

// Función para guardar el nombre del jugador
function guardarNombre() {
    const nombreInput = document.getElementById('nombre').value;
    if (nombreInput) {
        localStorage.setItem('nombreJugador', nombreInput); // Guardar en localStorage
        document.getElementById('nombreUsuario').style.display = 'none';
        mostrarNumeroPreguntas();
    } else {
        alert("Introduce un nombre válido.");
    }
}

// Función para mostrar la sección de número de preguntas
function mostrarNumeroPreguntas() {
    document.getElementById('numeroPreguntas').style.display = 'block';
    document.getElementById('guardarCantidad').addEventListener('click', guardarCantidad);
}

// Función para guardar la cantidad de preguntas
function guardarCantidad() {
    const cantidadInput = parseInt(document.getElementById('cantidadPreguntas').value);
    if (cantidadInput >= 1 && cantidadInput <= 10) {
        estatDeLaPartida.totalPreguntas = cantidadInput;
        document.getElementById('numeroPreguntas').style.display = 'none';
        iniciarJoc();
    } else {
        alert("Introduce un número de preguntas válido (1-10).");
    }
}

// Función para iniciar el jeugo
function iniciarJoc() {
    document.getElementById('juego').style.display = 'block';
    let tiempo = 30;
    const timerElement = document.getElementById('time');
    const interval = setInterval(() => {
        tiempo--;
        timerElement.textContent = tiempo;

        if (tiempo <= 0) {
            clearInterval(interval);
            puntuacionFinal();
        }
    }, 1000);

    fetch(`../back/getPreguntes.php?num=${estatDeLaPartida.totalPreguntas}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la respuesta del servidor: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (Array.isArray(data.preguntes)) {
                estatDeLaPartida.preguntes = data.preguntes.map(p => ({ ...p, respondida: false }));
                mostrarPregunta(estatDeLaPartida.preguntaActual);
            } else {
                throw new Error('Formato de datos inesperado');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('juego').innerHTML = `<p>Ha habido un error al cargar las preguntas. Por favor, inténtalo de nuevo.</p>`;
        });
}

// Función para mostrar la pregunta actual
function mostrarPregunta(index) {
    if (index < estatDeLaPartida.preguntes.length) {
        let pregunta = estatDeLaPartida.preguntes[index];
        let opcions = ['A', 'B', 'C', 'D'];
        let htmlString = `<div class="pregunta">`;
        htmlString += `${index + 1}. ${pregunta.pregunta}<br>`;
        htmlString += `<img src="${pregunta.imatge}" width="100"><br><br>`;

        for (let j = 0; j < pregunta.respostes.length; j++) {
            htmlString += `<button class="answer-button" data-index="${index}" data-respuesta="${j}" data-correcta="${pregunta.respostes[j].correcta}">${opcions[j]}) ${pregunta.respostes[j].resposta}</button><br>`;
        }
        htmlString += `</div>`;
        document.getElementById("partida").innerHTML = htmlString;

        document.querySelectorAll('.answer-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const preguntaIndex = parseInt(e.target.getAttribute('data-index'));
                const respuestaIndex = parseInt(e.target.getAttribute('data-respuesta'));
                const correcta = e.target.getAttribute('data-correcta') === 'true';

                reaccio(preguntaIndex, respuestaIndex, correcta);
            });
        });
    } else {
        puntuacionFinal();
    }
}

// Función para gestionar la respuesta y actualizar el marcador
function reaccio(preguntaIndex, respuestaIndex) {
    const pregunta = estatDeLaPartida.preguntes[preguntaIndex];

    // Verifica si la pregunta ya fue respondida
    if (pregunta.respondida) {
        console.log(`La pregunta ${preguntaIndex + 1} ya ha sido respondida.`);
        return;
    }

    estatDeLaPartida.preguntes[preguntaIndex].respondida = true;

    // Verifica si la respuesta seleccionada es correcta
    const respuestaSeleccionada = pregunta.respostes[respuestaIndex];
    pregunta.correcta = respuestaSeleccionada.correcta; // Guardar si la respuesta fue correcta

    if (respuestaSeleccionada.correcta) {
        estatDeLaPartida.contadorRespostas++;
        console.log(`Has respondido la pregunta ${preguntaIndex + 1}.`);
    } else {
        console.log(`Has respondido la pregunta ${preguntaIndex + 1}.`);
    }

    // Aumenta el contador de preguntas respondidas
    estatDeLaPartida.contadorPreguntes++;
    // Muestra la siguiente pregunta
    estatDeLaPartida.preguntaActual++;
    mostrarPregunta(estatDeLaPartida.preguntaActual);
    actualitzarMarcador();
}

// Función para actualizar el marcador
function actualitzarMarcador() {
    let htmlString = `<h2>Estado de la Pregunta:</h2>`;
    htmlString += '<table>';

    for (let i = 0; i < estatDeLaPartida.preguntes.length; i++) {
        let pregunta = estatDeLaPartida.preguntes[i];
        // Comprueba si la pregunta fue respondida y si es correcta o incorrecta
        htmlString += `<tr>
            <td>Pregunta ${i + 1}</td>
            <td>${pregunta.respondida ? 'Contestado' : 'Pendiente'}</td>
        </tr>`;
    }

    htmlString += '</table>';
    document.getElementById("marcador").innerHTML = htmlString;
}

let resul = false;

// Función para el mensaje final
function puntuacionFinal() {
    document.getElementById('juego').style.display = 'none';
    const nombreJugador = localStorage.getItem('nombreJugador');

    // Limpiar resultados anteriores
    document.getElementById('resultadoRespuestas').innerHTML = '';
    document.getElementById('mensajeFinal').textContent = '';
    document.getElementById('resultadoFinal').style.display = 'block';
    
    if (!resul) {
        const mostrarResultadosBtn = document.createElement('button');
        mostrarResultadosBtn.textContent = 'Mostrar Resultados';
        mostrarResultadosBtn.addEventListener('click', () => {
            mostrarResultados(nombreJugador);
            mostrarResultadosBtn.style.display = 'none';
        });
        document.getElementById('resultadoFinal').appendChild(mostrarResultadosBtn);
        resul = true;
    }

    actualitzarMarcador();
}

function mostrarResultadosConsola(nombreJugador, preguntasCorrectas, preguntasIncorrectas, totalPreguntas) {
    console.log(`Nombre del Jugador: ${nombreJugador}`);
    console.log(`Total de Preguntas: ${totalPreguntas}`);
    console.log(`Preguntas Correctas: ${preguntasCorrectas}`);
    console.log(`Preguntas Incorrectas: ${preguntasIncorrectas}`);
}

// Función para mostrar resultados
function mostrarResultados(nombreJugador) {
    const resultadoDiv = document.getElementById('resultadoRespuestas');
    
    if (resultadoDiv) {
        resultadoDiv.innerHTML = ''; // Limpiar resultados anteriores
        
        let htmlString = '<h3>Resultados de las Preguntas:</h3><ul>';
        
        const preguntasCorrectas = estatDeLaPartida.contadorRespostas;
        const preguntasIncorrectas = estatDeLaPartida.totalPreguntas - preguntasCorrectas;

        const mensajeFinal = `${nombreJugador} tiene ${preguntasCorrectas} / ${estatDeLaPartida.totalPreguntas} preguntas correctas.`;
        
        document.getElementById('mensajeFinal').textContent = mensajeFinal;

        estatDeLaPartida.preguntes.forEach((pregunta, index) => {
            const estado = pregunta.respondida ? (pregunta.correcta ? 'Correcta' : 'Incorrecta') : 'Pendiente';
            htmlString += `<li>Pregunta ${index + 1}: ${estado}</li>`;
        });
        htmlString += '</ul>';
        
        resultadoDiv.innerHTML = htmlString;
        resultadoDiv.style.display = 'block';

        // Mostrar resultados por consola
        mostrarResultadosConsola(nombreJugador, preguntasCorrectas, preguntasIncorrectas, estatDeLaPartida.totalPreguntas);
    } else {
        console.error('El contenedor de resultados no se encontró.');
    }
}

// Función para obtener el valor de las cookies
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`); 
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// Función para reiniciar el juego
function reiniciarJuego() {
    fetch('../back/finalitza.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.clear();
                localStorage.clear();

                // Reiniciar el estado del juego
                estatDeLaPartida = {
                    preguntes: [],
                    contadorRespostas: 0,
                    contadorPreguntes: 0,
                    preguntaActual: 0,
                    totalPreguntas: 0
                };

                // Limpiar la interfaz
                document.getElementById('marcador').innerHTML = '';
                document.getElementById('estado').innerHTML = '';
                document.getElementById('time').innerText = '30';
                document.getElementById('resultadoRespuestas').innerHTML = '';
                document.getElementById('mensajeFinal').textContent = '';
               
                // Mostrar las secciones necesarias
                document.getElementById('juego').style.display = 'none';
                document.getElementById('resultadoFinal').style.display = 'none'; // Asegúrate de que esto esté aquí
                document.getElementById('numeroPreguntas').style.display = 'none';
                document.getElementById('nombreUsuario').style.display = 'block';
                
                // Reiniciar el estado de la variable
                resul = false;

                document.getElementById('nombre').value = '';
                 document.getElementById('cantidadPreguntas').value = '';
               
                 verificarNombre();
            } else {
                console.error('No se pudo finalizar la sesión:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al finalizar la sesión:', error);
        });
}

document.addEventListener('DOMContentLoaded', () => {
    verificarNombre();
    document.getElementById('reiniciarJuego').addEventListener('click', reiniciarJuego);
});

// FUNCIONES PARA EL CRUD

function listarPreguntas() {
    fetch('../back/listarPreguntes.php')
    .then(response => response.text())
    .then(html => {
        document.body.innerHTML = html;
        addEventListenersToButtons();
    })
    .catch(error => console.error('Error:', error));
}

function anadirPregunta() {
    fetch('../back/addPregunte.php')
    .then(response => response.text())
    .then(html => {
        document.body.innerHTML = html;
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(form);
                
                fetch('../back/addPregunte.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    document.body.innerHTML = html;
                    addEventListenersToButtons();
                })
                .catch(error => console.error('Error al enviar el formulario:', error));
            });
        }
        addEventListenersToButtons();
    })
    .catch(error => console.error('Error al cargar el formulario:', error));
}

function editarPregunta(id) {
    fetch(`../back/editPregunte.php?id=${id}`)
    .then(response => response.text())
    .then(html => {
        document.body.innerHTML = html;
        const form = document.querySelector('#editPreguntaForm');
        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(form);
                
                fetch(`../back/editPregunte.php?id=${id}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        listarPreguntas();
                    } else {
                        alert('Error: ' + (data.message || 'No se pudo actualizar la pregunta'));
                    }
                })
                .catch(error => {
                    console.error('Error al enviar el formulario:', error);
                    alert('Error al actualizar la pregunta');
                });
            });
        }
        addEventListenersToButtons();
    })
    .catch(error => console.error('Error al cargar el formulario de edición:', error));
}

function eliminarPregunta(id) {
    if (confirm('¿Estás seguro de eliminar esta pregunta?')) {
        fetch(`../back/deletePregunte.php?id=${id}`)
        .then(response => response.text())
        .then(() => {
            listarPreguntas(); // Recargar la lista de preguntas
        })
        .catch(error => console.error('Error:', error));
    }
}

function addEventListenersToButtons() {
    const editButtons = document.querySelectorAll('.edit-button');
    const deleteButtons = document.querySelectorAll('.delete-button');
    const volverInicioButton = document.querySelector('#volverInicio');
    const volverListaButton = document.querySelector('#volverLista');

    editButtons.forEach(button => {
        button.addEventListener('click', () => editarPregunta(button.dataset.id));
    });
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', () => eliminarPregunta(button.dataset.id));
    });

    if (volverInicioButton) {
        volverInicioButton.addEventListener('click', listarPreguntas);
    }

    if (volverListaButton) {
        volverListaButton.addEventListener('click', listarPreguntas);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    verificarNombre();
    document.getElementById('reiniciarJuego').addEventListener('click', reiniciarJuego);
    
    // Nuevos event listeners para los botones de administración
    document.getElementById('listarPreguntas').addEventListener('click', listarPreguntas);
    document.getElementById('anadirPregunta').addEventListener('click', anadirPregunta);
    document.getElementById('anadirPregunta').addEventListener('click', anadirPregunta);
});

