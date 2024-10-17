<?php
session_start();

// Verificar si el jugador ya está en la sesión
if (isset($_SESSION['jugador'])) {
    // Devolver el estado actual de la sesión
    echo json_encode([
        'jugador' => $_SESSION['jugador'],
        'puntuacion' => $_SESSION['puntuacion'] ?? 0, 
        'correctas' => $_SESSION['correctas'] ?? 0, 
        'incorrectas' => $_SESSION['incorrectas'] ?? 0, 
        'numPreguntas' => $_SESSION['numPreguntas'] ?? 10
    ]);
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['jugador'] = $_POST['nombre'];
    $_SESSION['puntuacion'] = 0; 
    $_SESSION['correctas'] = 0; 
    $_SESSION['incorrectas'] = 0; 
    $_SESSION['pregActual'] = 0;
    $_SESSION['numPreguntas'] = (int)$_POST['numPreguntas']; 

    echo json_encode([
        'jugador' => $_SESSION['jugador'],
        'numPreguntas' => $_SESSION['numPreguntas']
    ]);
    exit;
}
?>