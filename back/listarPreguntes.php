<?php
require_once 'conexio.php';

// Consulta las preguntas
$sql = "SELECT * FROM preguntas";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
}