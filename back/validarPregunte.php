<?php
session_start();

if (isset($_SESSION['preguntas'])) {
    $preguntas = $_SESSION['preguntas'];
    $respuestasCorrectas = 0;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuestas'])) {
        $respuestasUsuario = $_POST['respuestas'];
        
        foreach ($preguntas as $index => $pregunta) {
            $correcta = $pregunta['respostes'][$pregunta['correcta']];
            if (isset($respuestasUsuario[$index]) && $respuestasUsuario[$index] === $correcta['resposta']) {
                $respuestasCorrectas++;
            }
        }

        echo "Total de preguntas: " . count($preguntas) . "<br>";
        echo "Respuestas correctas: " . $respuestasCorrectas . "<br>";
    }
} else {
    echo "No hay preguntas disponibles para validar.";
}
?>
