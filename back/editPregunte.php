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
    
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Editar Pregunta</title>
    </head>
    <body>
        <h1>Editar Pregunta</h1>
    
        <form id="editPreguntaForm">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <label for="pregunta">Pregunta:</label>
            <input type="text" id="pregunta" name="pregunta" value="<?php echo htmlspecialchars($pregunta['pregunta']); ?>" required><br>
    
            <label for="imatge">Imagen (URL):</label>
            <input type="text" id="imatge" name="imatge" value="<?php echo htmlspecialchars($pregunta['imatge']); ?>" required><br>
            
            <?php foreach ($respuestas as $index => $respuesta): ?>
                <input type="hidden" name="respuesta_id<?php echo $index + 1; ?>" value="<?php echo $respuesta['id']; ?>">
                <label for="respuesta<?php echo $index + 1; ?>">Opción <?php echo $index + 1; ?>:</label>
                <input type="text" id="respuesta<?php echo $index + 1; ?>" name="respuesta<?php echo $index + 1; ?>" value="<?php echo htmlspecialchars($respuesta['resposta']); ?>" required><br>
            <?php endforeach; ?>
            
            <label for="correcta">Respuesta Correcta:</label>
            <select id="correcta" name="correcta" required>
                <?php foreach ($respuestas as $index => $respuesta): ?>
                    <option value="<?php echo $index; ?>" <?php echo ($respuesta['correcta'] == 1) ? 'selected' : ''; ?>>Opción <?php echo $index + 1; ?></option>
                <?php endforeach; ?>
            </select><br>
            
            <button type="submit">Actualizar Pregunta</button>
        </form>
    
        <button id="volverLista">Volver a la lista de preguntas</button>
    </body>
    </html>