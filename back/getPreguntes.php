<?php
header('Content-Type: application/json');

include('conexio.php');
include('session.php');

$numPreguntes = isset($_GET['num']) ? intval($_GET['num']) : 10;

// Conectar a la abse de datos
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connexió fallida: ' . $conn->connect_error]));
}

// Consulta para obtener todas las preguntas
$consultaPreguntas = "SELECT * FROM preguntas ORDER BY RAND() LIMIT ?"; 
$stmt = $conn->prepare($consultaPreguntas);
$stmt->bind_param("i", $numPreguntes);
$stmt->execute();
$resultadoPreguntas = $stmt->get_result();

$totesPreguntes = [];

if ($resultadoPreguntas->num_rows > 0) {
    while ($row = $resultadoPreguntas->fetch_assoc()) {
        $idPregunta = $row['id'];
        
        // Obtener las respuestas por cada pregunta
        $consultaRespuestas = "SELECT * FROM respuestas WHERE pregunta_id = ?";
        $stmt = $conn->prepare($consultaRespuestas);
        $stmt->bind_param("i", $idPregunta);
        $stmt->execute();
        $resultadoRespuestas = $stmt->get_result();
        
        $respuestas = [];
        while ($respuesta = $resultadoRespuestas->fetch_assoc()) {
            $respuestas[] = [
                'id' => $respuesta['id'],
                'resposta' => $respuesta['resposta'],
                'correcta' => $respuesta['correcta'] == 1
            ];
        }
        $row['respostes'] = $respuestas;
        $totesPreguntes[] = $row;
    }

    $_SESSION['preguntesSeleccionades'] = $totesPreguntes;
}

$output = ob_get_clean();

$response = [
    'preguntes' => $totesPreguntes,
    'contadorRespostas' => isset($_SESSION['contadorRespostas']) ? $_SESSION['contadorRespostas'] : 0,
    'contadorPreguntes' => isset($_SESSION['contadorPreguntes']) ? $_SESSION['contadorPreguntes'] : 0,
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);

$conn->close();
?>