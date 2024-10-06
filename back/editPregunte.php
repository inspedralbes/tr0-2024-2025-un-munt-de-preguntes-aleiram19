<?php
require_once 'conexio.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mensaje = '';

if ($id <= 0) {
    header('Location: listarPreguntes.php');
    exit();
}

// Para obtener la pregunta
$stmt = $conn->prepare("SELECT * FROM preguntas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pregunta = $stmt->get_result()->fetch_assoc();

if (!$pregunta) {
    header('Location: listarPreguntes.php');
    exit();
}

// Para obtener las respuestas
$stmt = $conn->prepare("SELECT * FROM respuestas WHERE pregunta_id = ? ORDER BY id");
$stmt->bind_param("i", $id);
$stmt->execute();
$respuestas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Para procesar el formulario
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

    // Para actualizar la pregunta
    $stmt = $conn->prepare("UPDATE preguntas SET pregunta = ?, imatge = ? WHERE id = ?");
    $stmt->bind_param("ssi", $preguntaTexto, $imatge, $id);
    $stmt->execute();

    // Actualizar las respuestas
    $stmt = $conn->prepare("UPDATE respuestas SET resposta = ?, correcta = ? WHERE id = ?");
    foreach ($respuestas as $index => $respuesta) {
        $es_correcta = ($index == $correcta) ? 1 : 0;
        $stmt->bind_param("sii", $respuestas_nuevas[$index], $es_correcta, $respuesta['id']);
        $stmt->execute();
    }

    header('Location: listarPreguntes.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Pregunta</title>
    <link rel="stylesheet" type="text/css" href="/web/frontend/styles.css">
</head>
<body>
    <h1>Editar Pregunta</h1>

    <form action="editPregunte.php?id=<?php echo $id; ?>" method="post">
        <label for="pregunta">Pregunta:</label>
        <input type="text" id="pregunta" name="pregunta" value="<?php echo htmlspecialchars($pregunta['pregunta']); ?>" required><br>

        <label for="imatge">Imagen (URL):</label>
        <input type="text" id="imatge" name="imatge" value="<?php echo htmlspecialchars($pregunta['imatge']); ?>" required><br>
        
        <?php foreach ($respuestas as $index => $respuesta): ?>
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

    <a href="listarPreguntes.php">Volver a la lista de preguntas</a>
</body>
</html>