<?php
/**
 * @author    Bartolomé Sintes Marco - bartolome.sintes+mclibre@gmail.com
 * @license   https://www.gnu.org/licenses/agpl-3.0.txt AGPL 3 or later
 * @link      https://www.mclibre.org
 */

require_once "../comunes/biblioteca.php";

session_name("sesiondb");
session_start();

if (!isset($_SESSION["conectado"])) {
    header("Location:../index.php");
    exit;
}

$pdo = conectaDb();

cabecera("Personas - Modificar 3");

$nombre    = recoge("nombre");
$apellidos = recoge("apellidos");
$telefono  = recoge("telefono");
$correo    = recoge("correo");
$genero = recoge("genero");
$id        = recoge("id");

if ($id == "") {
    print "    <p class=\"aviso\">No record has been selected.</p>\n";
} else {
    $idOk = true;
}

// Comprobamos que no se intenta crear un registro vacío
$registroNoVacioOk = false;

    if ($nombre == "" || $apellidos == "" || $telefono == "" || $correo == "" || $genero == "") {
        print "    <p class=\"aviso\">You must fill in at least one of the fields. The record has not been saved.</p>\n";
        print "\n";
    } else {
        $registroNoVacioOk = true;
}

// Comprobamos que el registro con el id recibido existe en la base de datos
$registroEncontradoOk = false;

if ($idOk && $registroNoVacioOk) {
    $consulta = "SELECT COUNT(*) FROM personas
                 WHERE id = :id";

    $resultado = $pdo->prepare($consulta);
    if (!$resultado) {
        print "    <p class=\"aviso\">Error preparing the query. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } elseif (!$resultado->execute([":id" => $id])) {
        print "    <p class=\"aviso\">Error executing the query. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } elseif ($resultado->fetchColumn() == 0) {
        print "    <p class=\"aviso\">Record not found.</p>\n";
    } else {
        $registroEncontradoOk = true;
    }
}

// Si todas las comprobaciones han tenido éxito ...
if ($idOk && $registroNoVacioOk && $registroEncontradoOk) {
    // Actualizamos el registro con los datos recibidos
    $consulta = "UPDATE personas
                 SET nombre = :nombre, apellidos = :apellidos, telefono = :telefono, correo = :correo, genero = :genero
                 WHERE id = :id";

    $resultado = $pdo->prepare($consulta);
    if (!$resultado) {
        print "    <p class=\"aviso\">Error preparing the query. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } elseif (!$resultado->execute([":nombre" => $nombre, ":apellidos" => $apellidos, ":telefono" => $telefono, ":correo" => $correo, ":genero" => $genero, ":id" => $id])) {
        print "    <p class=\"aviso\">Error executing the query. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } else {
        print "    <p>Record modified successfully.</p>\n";
    }
}

pie();