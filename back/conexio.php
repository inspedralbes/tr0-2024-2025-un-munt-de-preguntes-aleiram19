<?php
//PRODUCCION
$server = "localhost:3306"; 
$username = "a23aleminram_aleiram";
$password = "Ramos190598@";
$bd = "a23aleminram_quiz"; 

//LOCAL
//$server = "localhost"; 
//$username = "root"; 
//$password = ""; 
//$bd = "quiz"; 

$conn = new mysqli($server, $username, $password, $bd);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    //echo "Conexión exitosa a la base de datos $bd.";
}
?>