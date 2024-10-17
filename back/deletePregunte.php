<?php
require_once 'conexio.php';

$id = $_GET['id'];

$sql = "DELETE FROM respuestas WHERE pregunta_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$sql = "DELETE FROM preguntas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: listarPreguntes.php');
exit;
?>
