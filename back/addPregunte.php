<?php
require_once 'conexio.php'; 

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Procesar los datos del formulario
    $pregunta = $_POST['pregunta'];
    $imatge = $_POST['imatge'];
    $respuesta1 = $_POST['respuesta1'];
    $respuesta2 = $_POST['respuesta2'];
    $respuesta3 = $_POST['respuesta3'];
    $respuesta4 = $_POST['respuesta4'];
    $correcta = $_POST['correcta']; 

    $stmt_pregunta = $conn->prepare("INSERT INTO preguntas (pregunta, imatge) VALUES (?, ?)");
    $stmt_pregunta->bind_param("ss", $pregunta, $imatge);

    if ($stmt_pregunta->execute()) {
        $pregunta_id = $stmt_pregunta->insert_id;

        $stmt_respuesta = $conn->prepare("INSERT INTO respuestas (pregunta_id, resposta, correcta) VALUES (?, ?, ?)");

        $respuestas = [$respuesta1, $respuesta2, $respuesta3, $respuesta4];

        foreach ($respuestas as $index => $resposta) {
            $es_correcta = ($index == $correcta) ? 1 : 0;
            $stmt_respuesta->bind_param("isi", $pregunta_id, $resposta, $es_correcta);
            $stmt_respuesta->execute();
        }

        $mensaje = "Pregunta añadida con éxito.";
    } else {
        $mensaje = "Error: " . $stmt_pregunta->error;
    }

    $stmt_pregunta->close(); 
    $stmt_respuesta->close();
}
