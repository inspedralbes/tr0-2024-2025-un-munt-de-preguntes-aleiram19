<?php
$server = "localhost:3306"; //localhost:3306
$username = "root"; //a23aleminram_aleiram
$password = ""; //Ramos190598@
$bd = "quiz"; //a23aleminram_quiz

$conn = new mysqli($server, $username, $password, $bd);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosa a la base de datos $bd.";
}
?>