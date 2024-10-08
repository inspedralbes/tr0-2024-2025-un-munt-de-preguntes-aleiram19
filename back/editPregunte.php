<?php
require_once 'conexio.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $preguntaTexto = $_POST['pregunta'];
    $imatge = $_POST['imatge'];
    $respuestas_nuevas = [
        $_POST['respuesta1'],
        $_POST['respuesta2'],
        $_POST['respuesta3'],
        $_POST['respuesta4']
    ];
    $correcta = $_POST['correcta'];

    // Actualizar la pregunta
    $stmt = $conn->prepare("UPDATE preguntas SET pregunta = ?, imatge = ? WHERE id = ?");
    $stmt->bind_param("ssi", $preguntaTexto, $imatge, $id);
    $result = $stmt->execute();

    if ($result) {
        // Actualizar las respuestas
        $stmt = $conn->prepare("UPDATE respuestas SET resposta = ?, correcta = ? WHERE pregunta_id = ? AND id = ?");
        foreach ($respuestas_nuevas as $index => $respuesta) {
            $respuesta_id = $_POST['respuesta_id' . ($index + 1)]; // Asegúrate de que estos campos existan en el formulario
            $es_correcta = ($index == $correcta) ? 1 : 0;
            $stmt->bind_param("siii", $respuesta, $es_correcta, $id, $respuesta_id);
            $stmt->execute();
        }
        $mensaje = "Pregunta actualizada con éxito.";
        echo json_encode(['success' => true, 'message' => $mensaje]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la pregunta']);
    }
    exit();
}

if ($id <= 0) {
    die("ID de pregunta inválido");
}
    $stmt = $conn->prepare("SELECT * FROM preguntas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $pregunta = $stmt->get_result()->fetch_assoc();
    
    if (!$pregunta) {
        die("Pregunta no encontrada");
    }
    
    // Obtener las respuestas
    $stmt = $conn->prepare("SELECT * FROM respuestas WHERE pregunta_id = ? ORDER BY id");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $respuestas = [];
    while ($respuesta = $result->fetch_assoc()) {
        $respuestas[] = $respuesta;
    }