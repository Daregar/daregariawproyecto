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

cabecera("Personas - Añadir 2");

$nombre    = recoge("nombre");
$apellidos = recoge("apellidos");
$telefono  = recoge("telefono");
$correo    = recoge("correo");
$genero = recoge("genero");



// Comprobamos que no se intenta crear un registro vacío
$registroNoVacioOk = false;

    if ($nombre == "" || $apellidos == "" || $telefono == "" || $correo == "" || $genero == "") {
        print "    <p class=\"aviso\">You must fill in at least one of the fields. The record has not been saved.</p>\n";
        print "\n";
    } else {
        $registroNoVacioOk = true;
    }

// Comprobamos que no se intenta crear un registro idéntico a uno que ya existe
$registroDistintoOk = false;

if ($registroNoVacioOk) {
    $consulta = "SELECT COUNT(*) FROM personas
                 WHERE nombre = :nombre
                 AND apellidos = :apellidos
                 AND telefono = :telefono
                 AND correo = :correo
                 AND genero = :genero";

    $resultado = $pdo->prepare($consulta);
    if (!$resultado) {
        print "    <p class=\"aviso\">Error preparing the query. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } elseif (!$resultado->execute([":nombre" => $nombre, ":apellidos" => $apellidos, ":telefono" => $telefono, ":correo" => $correo, ":genero" => $genero])) {
        print "    <p class=\"aviso\">Error executing the query. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } elseif ($resultado->fetchColumn() > 0) {
        print "    <p class=\"aviso\">The record already exists.</p>\n";
    } else {
        $registroDistintoOk = true;
    }
}

// Si todas las comprobaciones han tenido éxito ...
if ($registroNoVacioOk && $registroDistintoOk ) {
    // Insertamos el registro en la tabla
    $consulta = "INSERT INTO personas
                 (nombre, apellidos, telefono, correo, genero)
                 VALUES (:nombre, :apellidos, :telefono, :correo, :genero)";

    $resultado = $pdo->prepare($consulta);
    if (!$resultado) {
        print "    <p class=\"aviso\">Error preparing the query. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } elseif (!$resultado->execute([":nombre" => $nombre, ":apellidos" => $apellidos, ":telefono" => $telefono, ":correo" => $correo, ":genero" => $genero])) {
        print "    <p class=\"aviso\">Error executing the query. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } else {
        print "    <p>Record created successfully.</p>\n";
    }
}

pie();