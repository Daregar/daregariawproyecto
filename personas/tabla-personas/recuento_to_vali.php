<?php
require_once '../comunes/biblioteca.php';
session_name("sesiondb");
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar que se ha proporcionado el parámetro de confirmación
if (!isset($_POST['confirmar']) || $_POST['confirmar'] !== 'si') {
    die('<p>Error: You must confirm the query.</p>');
}

// Redirigir a la operación principal para consultar los datos
header('Location: recuento_to.php?confirmado=si');
exit();
?>
