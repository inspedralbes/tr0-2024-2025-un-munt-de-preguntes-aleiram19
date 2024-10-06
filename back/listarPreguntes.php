<?php
require_once 'conexio.php';

// Primero consulta las preguntas
$sql = "SELECT * FROM preguntas";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
// Luego recoge las preguntas en un array
$preguntas = [];
while ($row = $result->fetch_assoc()) {
    $preguntas[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Preguntas</title>
    <link rel="stylesheet" type="text/css" href="/quiz/frontend/styles.css">
</head>
<body>
    <h1>Lista de Preguntas</h1>
    <a href="addPregunte.php">Añadir Nueva Pregunta</a>
    <button onclick="window.location.href='/quiz/frontend/index.html'">Volver al Inicio</button>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Pregunta</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($preguntas as $pregunta): ?>
            <tr>
                <td><?php echo $pregunta['id']; ?></td>
                <td><?php echo $pregunta['pregunta']; ?></td>
                <td>
                    <a href="editPregunte.php?id=<?php echo $pregunta['id']; ?>">Editar</a>
                    <a href="deletePregunte.php?id=<?php echo $pregunta['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar esta pregunta?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>