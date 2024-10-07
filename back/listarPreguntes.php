<?php
require_once 'conexio.php';

// Consulta las preguntas
$sql = "SELECT * FROM preguntas";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}

// GENERO MI HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Preguntas</title>
</head>
<body>
    <h1>Lista de Preguntas</h1>
    <button id="anadirPreguntaBtn">Añadir Nueva Pregunta</button>
    <button id="reiniciarJuego">Volver al Inicio</button>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Pregunta</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['pregunta']; ?></td>
                <td>
                    <button class="edit-button" data-id="<?php echo $row['id']; ?>">Editar</button>
                    <button class="delete-button" data-id="<?php echo $row['id']; ?>">Eliminar</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>