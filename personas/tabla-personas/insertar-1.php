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

cabecera("Personas - Añadir 1");

    // Mostramos el formulario
?>
<form action="insertar-2.php" method="post">
  <p>Enter the details of the new record:</p>

  <table>
    <tr>
      <td>Name:</td>
      <td><input type="text" name="nombre" autofocus></td>
    </tr>
    <tr>
      <td>Surname:</td>
      <td><input type="text" name="apellidos"></td>
    </tr>
    <tr>
      <td>Phone:</td>
      <td><input type="text" name="telefono"></td>
    </tr>
    <tr>
      <td>Email:</td>
      <td><input type="text" name="correo"></td>
    </tr>
    <tr>
      <td>Gender:</td>
      <td><input type="text" name="genero"></td>
    </tr>
  </table>
  <p>
    <input type="submit" value="Add">
    <input type="reset" value="Reset form">
  </p>
</form>
<?php

pie();
?>
