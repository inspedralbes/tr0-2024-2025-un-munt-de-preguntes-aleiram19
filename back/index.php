<?php
session_start();
echo '<h1> BIENVENIDO AL QUIZ DE PELICULAS </h1>';
$_SESSION['instante'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Películas</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <form action="index2.php" method="POST">
        Nombre del jugador: <input type="text" name="name" required><br>
        <input type="submit" value="Iniciar Quiz">
    </form>
</body>
</html>