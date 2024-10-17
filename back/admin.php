<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'conexio.php'; 
require_once 'migrate.php'; 

if (!isset($_SESSION['nombreJugador'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre'])) {
        $_SESSION['nombreJugador'] = htmlspecialchars($_POST['nombre']);
    } else {
        echo '<form method="post">
                <label for="nombre">Introduce tu nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                <input type="submit" value="Iniciar">
              </form>';
        exit;
    }
}
?>
