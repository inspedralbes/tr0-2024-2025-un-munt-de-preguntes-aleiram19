<?php
require_once 'conexio.php';

// Crear las tablas si no existen
$sql_preguntas = "CREATE TABLE IF NOT EXISTS preguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta TEXT NOT NULL,
    imatge VARCHAR(255)
)";
$conn->query($sql_preguntas);

$sql_respuestas = "CREATE TABLE IF NOT EXISTS respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta_id INT,
    resposta TEXT NOT NULL,
    correcta BOOLEAN NOT NULL,
    FOREIGN KEY (pregunta_id) REFERENCES preguntas(id) ON DELETE CASCADE
)";
$conn->query($sql_respuestas);

// Lee las preguntas del archivo JSON
$jsonData = file_get_contents('peliculas.json');
$preguntas = json_decode($jsonData, true);

// Inserta las preguntas en la base de datos
foreach ($preguntas['preguntes'] as $pregunta) {
    $preguntaTexto = mysqli_real_escape_string($conn, $pregunta['pregunta']);
    $imatge = mysqli_real_escape_string($conn, $pregunta['imatge']);

    $sql_insert_pregunta = "INSERT INTO preguntas (pregunta, imatge) VALUES ('$preguntaTexto', '$imatge')";
    
    if (!$conn->query($sql_insert_pregunta)) {
        die("Error al insertar la pregunta: " . $conn->error);
    }

    // Obtener el ID de la pregunta insertada
    $pregunta_id = $conn->insert_id;

    // Insertar respuestas
    foreach ($pregunta['respostes'] as $resposta) {
        $respostaTexto = mysqli_real_escape_string($conn, $resposta['resposta']);
        $correcta = ($resposta['correcta'] == true) ? 1 : 0; // Asegúrate de que el valor sea un número (0 o 1)
        
        // Insertar respuesta
        $sql_insert_respuesta = "INSERT INTO respuestas (pregunta_id, resposta, correcta) VALUES ($pregunta_id, '$respostaTexto', $correcta)";
        
        if (!$conn->query($sql_insert_respuesta)) {
            die("Error al insertar la respuesta: " . $conn->error);
        }
    }
}
$conn->close();
?>