<?php
/**
 * @author    Bartolomé Sintes Marco - bartolome.sintes+mclibre@gmail.com
 * @license   https://www.gnu.org/licenses/agpl-3.0.txt AGPL 3 or later
 * @link      https://www.mclibre.org
 */


// Carga Biblioteca específica de la base de datos utilizada





function recoge($key, $type = "", $default = null, $allowed = null)
{
    if (!is_string($key) && !is_int($key) || $key == "") {
        trigger_error("Function recoge(): Argument #1 (\$key) must be a non-empty string or an integer", E_USER_ERROR);
    } elseif ($type !== "" && $type !== []) {
        trigger_error("Function recoge(): Argument #2 (\$type) is optional, but if provided, it must be an empty array or an empty string", E_USER_ERROR);
    } elseif (isset($default) && !is_string($default)) {
        trigger_error("Function recoge(): Argument #3 (\$default) is optional, but if provided, it must be a string", E_USER_ERROR);
    } elseif (isset($allowed) && !is_array($allowed)) {
        trigger_error("Function recoge(): Argument #4 (\$allowed) is optional, but if provided, it must be an array of strings", E_USER_ERROR);
    } elseif (is_array($allowed) && array_filter($allowed, function ($value) { return !is_string($value); })) {
        trigger_error("Function recoge(): Argument #4 (\$allowed) is optional, but if provided, it must be an array of strings", E_USER_ERROR);
    } elseif (!isset($default) && isset($allowed) && !in_array("", $allowed)) {
        trigger_error("Function recoge(): If argument #3 (\$default) is not set and argument #4 (\$allowed) is set, the empty string must be included in the \$allowed array", E_USER_ERROR);
    } elseif (isset($default, $allowed) && !in_array($default, $allowed)) {
        trigger_error("Function recoge(): If arguments #3 (\$default) and #4 (\$allowed) are set, the \$default string must be included in the \$allowed array", E_USER_ERROR);
    }

    if ($type == "") {
        if (!isset($_REQUEST[$key]) || (is_array($_REQUEST[$key]) != is_array($type))) {
            $tmp = "";
        } else {
            $tmp = trim(htmlspecialchars($_REQUEST[$key]));
        }
        if ($tmp == "" && !isset($allowed) || isset($allowed) && !in_array($tmp, $allowed)) {
            $tmp = $default ?? "";
        }
    } else {
        if (!isset($_REQUEST[$key]) || (is_array($_REQUEST[$key]) != is_array($type))) {
            $tmp = [];
        } else {
            $tmp = $_REQUEST[$key];
            array_walk_recursive($tmp, function (&$value) use ($default, $allowed) {
                $value = trim(htmlspecialchars($value));
                if ($value == "" && !isset($allowed) || isset($allowed) && !in_array($value, $allowed)) {
                    $value = $default ?? "";
                }
            });
        }
    }
    return $tmp;
}
/* 
Esta función pinta la parte superior de las páginas web
SI LA SESIÓN ESTÁ INICIADA: Saca el menú de las funciones que se pueden hacer en la base de datos + DESCONECTARSE
SI LA SESIÓN NO ESTÁ INICIADA: Saca exclusivamente el menu CONECTARSE 
*/
function cabecera($texto)
{
    print "<!DOCTYPE html>\n";
    print "<html lang=\"es\">\n";
    print "<head>\n";
    print "  <meta charset=\"utf-8\">\n";
    print "  <title>\n";
    print "    $texto. Bases de datos (3) 2. Bases de datos (3).\n";
    print "    Ejercicios. PHP. Bartolomé Sintes Marco. www.mclibre.org\n";
    print "  </title>\n";
    print "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
    print "  <link rel=\"stylesheet\" href=\"estilos.css\" title=\"Color\">\n";
    print "</head>\n";
    print "\n";
    print "<body>\n";
    print "  <header>\n";
    print "    <h1>Bases de datos (3) 2 - $texto</h1>\n";
    print "\n";
    print "    <nav>\n";
    print "      <ul>\n";
    if (!isset($_SESSION["conectado"])) {
      
            print "        <li><a href=\"login-1.php\">To connect</a></li>\n";
       
        } 
       
     else {
           
            print "        <li><a href=\"insertar-1.php\">Add record</a></li>\n";
            print "        <li><a href=\"resumen.php\">Summary</a></li>\n";
            print "        <li><a href=\"listar.php\">List</a></li>\n";
            print "        <li><a href=\"borrar-1.php\">Delete</a></li>\n";
            print "        <li><a href=\"buscar-1.php\">Search</a></li>\n";
            print "        <li><a href=\"recuento_to.php\">Count</a></li>\n";
            print "        <li><a href=\"exportar.php\">Export to csv</a></li>\n";
            print "        <li><a href=\"modificar-1.php\">Modify</a></li>\n";
            print "        <li><a href=\"borrar-todo-1.php\">Delete everything</a></li>\n";
            print "        <li><a href=\"../logout.php\">Disconnect</a></li>\n";
            
        } 
    print "      </ul>\n";
    print "    </nav>\n";
    print "  </header>\n";
    print "\n";
    print "  <main>\n";
}

function pie()
{
    print "  </main>\n";
    print "\n";
    print "  <footer>\n";
    print "    <p class=\"ultmod\">\n";
    print "      Última modificación de esta página:\n";
    print "      <time datetime=\"2024-02-20\">20 de febrero de 2024</time>\n";
    print "    </p>\n";
    print "\n";
    print "    <p class=\"licencia\">\n";
    print "      This program was made by David Arenas García.";
    print "    </p>\n";
    print "  </footer>\n";
    print "</body>\n";
    print "</html>\n";
}

// Funciones BASES DE DATOS
function conectaDb()
{
    

    try {
        $tmp = new PDO("mysql:host=localhost;dbname=db_iaw_dag;charset=utf8mb4", "admin", "admin123");
       return $tmp;
    }  catch (PDOException $e) {
        print "    <p class=\"aviso\">Error: No puede conectarse con la base de datos. {$e->getMessage()}</p>\n";
    } 

}

// MYSQL: Borrado y creación de base de datos y tablas

function borraTodo()
{
    global $pdo;

    print "    <p>Database Management System: MySQL.</p>\n";
    print "\n";

    $consulta = "DROP DATABASE IF EXISTS db_iaw_dag";

    if (!$pdo->query($consulta)) {
        print "    <p class=\"aviso\">Error deleting the database. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } else {
        print "    <p>Database deleted successfully (if it existed).</p>\n";
    }
    print "\n";

    $consulta = "CREATE DATABASE db_iaw_dag
                 CHARACTER SET utf8mb4
                 COLLATE utf8mb4_unicode_ci";

    if (!$pdo->query($consulta)) {
        print "    <p class=\"aviso\">Error creating the database. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
    } else {
        print "    <p>Database created successfully.</p>\n";
        print "\n";

        $consulta = "USE db_iaw_dag";

        if (!$pdo->query($consulta)) {
            print "    <p class=\"aviso\">Query error. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
        } else {
            print "    <p>Database selected successfully.</p>\n";
            print "\n";

            $consulta = "CREATE TABLE personas (
                         id INTEGER UNSIGNED AUTO_INCREMENT,
                         nombre VARCHAR (40),
                         apellidos VARCHAR (60),
                         telefono VARCHAR (15),
                         correo VARCHAR (50),
                         genero VARCHAR (45),
                         PRIMARY KEY(id)
                         )";

            if (!$pdo->query($consulta)) {
                print "    <p class=\"aviso\">Error creating the table. SQLSTATE[{$pdo->errorCode()}]: {$pdo->errorInfo()[2]}</p>\n";
            } else {
                print "    <p>Table created successfully.</p>\n";
            }
        }
    }
}

function encripta($cadena)
{
    

    return hash("sha256", $cadena);
}
