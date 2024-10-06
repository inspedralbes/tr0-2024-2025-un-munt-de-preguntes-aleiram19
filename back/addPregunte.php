<?php
require_once 'conexio.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Me recibe los datos
    $pregunta = $_POST['pregunta'];
    $imatge = $_POST['imatge']; // 
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

        header("Location: listarPreguntes.php");
        exit();
    } else {
        echo "Error: " . $stmt_pregunta->error;
    }

    $stmt_pregunta->close(); 
    $stmt_respuesta->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Nueva Pregunta</title>
    <link rel="stylesheet" type="text/css" href="/web/frontend/styles.css">
</head>
<body>
    <h1>Añadir Nueva Pregunta</h1>
    <form action="addPregunte.php" method="post">
        <label for="pregunta">Pregunta:</label>
        <input type="text" id="pregunta" name="pregunta" required><br>
        
        <label for="imatge">Imagen (URL):</label>
        <input type="text" id="imatge" name="imatge" required><br>

        <label for="respuesta1">Opción 1:</label>
        <input type="text" id="respuesta1" name="respuesta1" required><br>
        
        <label for="respuesta2">Opción 2:</label>
        <input type="text" id="respuesta2" name="respuesta2" required><br>
        
        <label for="respuesta3">Opción 3:</label>
        <input type="text" id="respuesta3" name="respuesta3" required><br>
        
        <label for="respuesta4">Opción 4:</label>
        <input type="text" id="respuesta4" name="respuesta4" required><br>
        
        <label for="correcta">Respuesta Correcta:</label>
        <select id="correcta" name="correcta" required>
            <option value="0">Opción 1</option>
            <option value="1">Opción 2</option>
            <option value="2">Opción 3</option>
            <option value="3">Opción 4</option>
        </select><br>
        
        <button type="submit">Insertar Pregunta</button>
    </form>

    <button onclick="window.location.href='/quiz/frontend/index.html'">Volver al Inicio</button>
</body>
</html>