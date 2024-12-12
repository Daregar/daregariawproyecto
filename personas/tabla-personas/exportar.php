<?php
// Iniciar sesión y configurar errores
session_name("sesiondb");
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../comunes/biblioteca.php';
cabecera("Exportar Contactos a CSV");

$exportacionRealizada = false;

// Verificar si la exportación ha sido confirmada
if (isset($_GET['confirmado']) && $_GET['confirmado'] === 'si') {
    $exportacionRealizada = true;

    // Esto evita la salida de contenido HTML antes de la descarga del archivo CSV
    ob_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=contactos_' . date('Ymd') . '.csv');

    $conexion = conectaDb();
    if (!$conexion) {
        die('Error: Could not connect to the database.');
    }

    // Obtener todos los datos de las personas
    $query = 'SELECT * FROM personas';
    $resultado = $conexion->query($query);
    if (!$resultado) {
        die('Error: Could not retrieve the data.');
    }

    $personas = $resultado->fetchAll(PDO::FETCH_ASSOC);

    // Abrir un archivo de salida en memoria
    $output = fopen('php://output', 'w');

    // Escribir la fila de encabezado
    fputcsv($output, array('ID', 'Nombre', 'Apellidos', 'Teléfono', 'Correo', 'Género'));

    // Escribir los datos de las personas
    foreach ($personas as $persona) {
        fputcsv($output, $persona);
    }

    // Cerrar el archivo de salida
    fclose($output);

    // Cerrar la conexión a la base de datos
    $conexion = null;

    // Asegúrate de que no se añada contenido adicional al archivo CSV
    exit();
}
?>

<?php if (!$exportacionRealizada): ?>
<!-- Formulario de confirmación -->
<form action="exportar_vali.php" method="POST">
    <p>Are you sure you want to export all contacts to a CSV file?</p>
    <input type="hidden" name="confirmar" value="si">
    <button type="submit">Confirm Export</button>
</form>
<?php endif; ?>

<p><a href="../index.php">Cancel and Return to the Start</a></p>

