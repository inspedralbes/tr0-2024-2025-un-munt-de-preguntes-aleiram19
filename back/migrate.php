<?php
require_once 'conexio.php';

// Crea las tablas si es que no existen
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

// Verifica si la tabla preguntas está vacía
$sql = "SELECT COUNT(*) as count FROM preguntas";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($row['count'] === 0) {
    // Lee las preguntas del archivo JSON
    $jsonData = file_get_contents('peliculas.json');
    $preguntas = json_decode($jsonData, true);

    // Luego inserta las preguntas en la base de datos
    foreach ($preguntas['preguntes'] as $pregunta) {
        // Inserta la pregunta
        $stmt_pregunta = $conn->prepare("INSERT INTO preguntas (pregunta, imatge) VALUES (?, ?)");
        $stmt_pregunta->bind_param("ss", $pregunta['pregunta'], $pregunta['imatge']);
        $stmt_pregunta->execute();
        
        $pregunta_id = $stmt_pregunta->insert_id;

        // Inserta las respuestas
        $stmt_respuesta = $conn->prepare("INSERT INTO respuestas (pregunta_id, resposta, correcta) VALUES (?, ?, ?)");
        
        // Inserta cada respuesta relacionada con cada pregunta
        foreach ($pregunta['respostes'] as $resposta) {
            $stmt_respuesta->bind_param("isi", $pregunta_id, $resposta['resposta'], $resposta['correcta']);
            $stmt_respuesta->execute();
        }
    }
}
?>