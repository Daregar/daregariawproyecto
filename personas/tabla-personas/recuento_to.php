<?php
// Iniciar sesión y configurar errores
session_name("sesiondb");
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir biblioteca y función cabecera
require_once '../comunes/biblioteca.php';
cabecera("Consultar Personas");

$consultaRealizada = false;

// Verificar si la consulta ha sido confirmada
if (isset($_GET['confirmado']) && $_GET['confirmado'] === 'si') {
    $consultaRealizada = true;

    // Conectar a la base de datos
    $conexion = conectaDb();
    if (!$conexion) {
        die('<p>Error: Could not connect to the database.</p>');
    }

    // Obtener el total de personas
    $queryTotal = 'SELECT COUNT(*) as total FROM personas';
    $resultadoTotal = $conexion->query($queryTotal);
    if ($resultadoTotal) {
        $filaTotal = $resultadoTotal->fetch(PDO::FETCH_ASSOC);
        $totalPersonas = htmlspecialchars($filaTotal['total']);
        echo "<p>Total number of people: $totalPersonas</p>";
    } else {
        echo "<p>Error retrieving the count of people.</p>";
    }

    // Obtener los nombres de todas las personas
    $queryNombres = 'SELECT nombre FROM personas';
    $resultadoNombres = $conexion->query($queryNombres);
    if ($resultadoNombres) {
        echo "<ul>";
        while ($filaNombre = $resultadoNombres->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>" . htmlspecialchars($filaNombre['nombre']) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Error retrieving the names of the people.</p>";
    }

    // Cerrar la conexión
    $conexion = null;
}
?>

<?php if (!$consultaRealizada): ?>
<!-- Formulario de confirmación -->
<form action="recuento_to_vali.php" method="POST">
    <p>Are you sure you want to query the total number of people and their names?</p>
    <input type="hidden" name="confirmar" value="si">
    <button type="submit">Confirm Query</button>
</form>
<?php endif; ?>

<p><a href="../index.php">Back to the beginning</a></p>

