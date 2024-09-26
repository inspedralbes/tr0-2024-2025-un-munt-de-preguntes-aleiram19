<?php
session_start();

// Inicializar variables de sesión si no existen
if (!isset($_SESSION['puntuacion'])) {
    $_SESSION['puntuacion'] = -1;
    $_SESSION['pregActual'] = -1; // Comenzar en la pregunta -1
}

// Se cargan las preguntas desde el archivo JSON
$QUIZ = file_get_contents('quiz.json');
$datos = json_decode($QUIZ, true);
$preguntas = $datos['questions'];

// Verificamos si se ha enviado una respuesta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuestaSeleccionada = $_POST['answer'];
    $preguntaActual = $_SESSION['pregActual'];

    // Comprobamos si la respuesta es correcta
    if ($respuestaSeleccionada == $preguntas[$preguntaActual]['correctIndex']) {
        $_SESSION['puntuacion']++;
    }
    $_SESSION['pregActual']++;
}

$indicePreguntaActual = $_SESSION['pregActual'];

// Comprobamos si se han respondido todas las preguntas
if ($indicePreguntaActual >= count($preguntas)) {
    // Finalizamos el quiz y mostramos la puntuación
    $puntuacionFinal = $_SESSION['puntuacion'];
    session_destroy(); // Reiniciar sesión
    echo "<h2>Quiz completado. Tu puntuación: $puntuacionFinal/" . count($preguntas) . "</h2>";
    echo '<a href="index.php">Volver a jugar</a>';
    exit;
}

// Mostrar la pregunta actual
$preguntaActual = $preguntas[$indicePreguntaActual];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Películas</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <h2>Pregunta <?php echo $indicePreguntaActual + 1; ?> de <?php echo count($preguntas); ?></h2> <!-- Contador correcto -->
    <p><?php echo $preguntaActual['question']; ?></p>
    
    <form action="index2.php" method="POST">
        <?php
        // Mostramos lasrespuestas
        for ($i = 0; $i < count($preguntaActual['answers']); $i++) {
            echo '<input type="radio" name="answer" value="' . $i . '" required>' . $preguntaActual['answers'][$i] . '<br>';
        }
        ?>
        <input type="submit" value="Enviar respuesta">
    </form>
</body>
</html>