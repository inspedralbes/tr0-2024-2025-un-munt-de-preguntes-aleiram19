<?php
session_start();

$_SESSION = array();
session_destroy();

// Devuelve una respuesta en formato JSON
echo json_encode(['status' => 'success', 'message' => 'Sesión finalizada correctamente']);
?>