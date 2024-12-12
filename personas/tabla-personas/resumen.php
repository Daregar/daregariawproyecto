<?php
// Iniciar sesión y configurar errores
session_name("sesiondb");
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir biblioteca y función cabecera
require_once '../comunes/biblioteca.php';
cabecera("Resumen Contactos");

// Conectar a la base de datos
$conexion = conectaDb();
if (!$conexion) {
    die('<p>Error: Could not connect to the database.</p>');
}

// Procesar la solicitud si se ha proporcionado una letra inicial
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['inicial']) && !empty($_GET['inicial'])) {
    $inicial = $_GET['inicial'];

    // Validar que la letra inicial es una sola letra
    if (!preg_match('/^[a-zA-Z]$/', $inicial)) {
       die('<p>Error: The initial must be a single letter.</p>');
    }

    // Obtener contactos que comienzan con la letra inicial 
    $query = 'SELECT * FROM personas WHERE LEFT(nombre, 1) = :inicial ORDER BY nombre';
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':inicial', $inicial, PDO::PARAM_STR);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultados) {
        echo '<h2>Contacts that start with the letter "' . htmlspecialchars($inicial) . '"</h2>';
        echo '<ul>';
        foreach ($resultados as $persona) {
            echo '<li>Name: ' . htmlspecialchars($persona['nombre']) . ' ' . htmlspecialchars($persona['apellidos']) . ' - Phone: ' . htmlspecialchars($persona['telefono']) . ' - Email: ' . htmlspecialchars($persona['correo']) . ' - Gender: ' . htmlspecialchars($persona['genero']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No contacts found with the initial "' . htmlspecialchars($inicial) . '".</p>';
    }
}

// Cerrar la conexión
$conexion = null;
?>

<!-- Formulario para introducir la letra inicial -->
<form action="resumen.php" method="GET">
    <label for="inicial">Enter the initial letter of the contact:</label>
    <input type="text" id="inicial" name="inicial" maxlength="1" required>
    <button type="submit">Generate Summary</button>
</form>

<p><a href="../index.php">Return to the start</a></p>

