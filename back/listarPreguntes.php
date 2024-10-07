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

// Si es una solicitud AJAX, solo devuelve el HTML de la tabla
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    ob_start();
?>
    <h1>Lista de Preguntas</h1>
    <button onclick="anadirPregunta()">Añadir Nueva Pregunta</button>
    <button onclick="volverInicio()">Volver al Inicio</button>

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
                    <button class="edit-button" data-id="<?php echo $pregunta['id']; ?>">Editar</button>
                    <button class="delete-button" data-id="<?php echo $pregunta['id']; ?>">Eliminar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php
    $html = ob_get_clean();
    echo $html;
    exit;
}

// Si no es una solicitud AJAX, muestra la página completa
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Preguntas</title>
    <link rel="stylesheet" type="text/css" href="/quiz/frontend/styles.css">
</head>
<body>
    <!-- Aquí va el mismo HTML que en la sección AJAX -->
    <script src="functions.js"></script>
</body>
</html>