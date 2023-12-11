<?php

# Conexión a la DB
function conexion()
{
    $pdo =  new PDO('mysql:host=localhost;dbname=inventario', 'root', '');
    return $pdo;
}


// Ejemplo de conexión y consulta

// try {
//     $pdo =  new PDO('mysql:host=localhost;dbname=inventario', 'root', 'root');

//     $pdo->query("INSERT INTO usuarios (usuario_id,nombre,apellido,usuario,password,email) VALUES (0,'Nano','Banano','nano_ban','123','nano@pet.com');");

//     foreach ($pdo->query('SELECT * from usuarios') as $fila) {
//         print_r($fila);
//     }
//     $pdo = null;
// } catch (PDOException $e) {
//     print "¡Error!: " . $e->getMessage() . "<br/>";
//     die();
// }

# Verificar datos
function verificar_datos($filtro, $cadena)
{
    if (preg_match("/^" . $filtro . "$/", $cadena)) {
        return false;
    } else {
        return true;
    }
}
# Ejemplo de verificación
/*
$nombre = "Carlos";

if (verificar_datos("[a-zA-Z]{6,10}", $nombre)) {
    echo "Los datos no coinciden";
}
*/

# Funciones para limpiar cadenas de texto
// Estas funciones se crean para evitar la inyección de cualquier tipo de código SQL, PHP o JS por cuestiones de seguridad

function limpiar_cadena($cadena)
{
    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);
    $cadena = str_ireplace("<script>", "", $cadena);
    $cadena = str_ireplace("</script>", "", $cadena);
    $cadena = str_ireplace("<script src", "", $cadena);
    $cadena = str_ireplace("<script type=", "", $cadena);
    $cadena = str_ireplace("SELECT * FROM", "", $cadena);
    $cadena = str_ireplace("DELETE FROM", "", $cadena);
    $cadena = str_ireplace("INSERT INTO", "", $cadena);
    $cadena = str_ireplace("DROP TABLE", "", $cadena);
    $cadena = str_ireplace("DROP DATABASE", "", $cadena);
    $cadena = str_ireplace("TRUNCATE TABLE", "", $cadena);
    $cadena = str_ireplace("SHOW TABLES;", "", $cadena);
    $cadena = str_ireplace("SHOW DATABASES;", "", $cadena);
    $cadena = str_ireplace("<?php", "", $cadena);
    $cadena = str_ireplace("?>", "", $cadena);
    $cadena = str_ireplace("--", "", $cadena);
    $cadena = str_ireplace("^", "", $cadena);
    $cadena = str_ireplace("<", "", $cadena);
    $cadena = str_ireplace("[", "", $cadena);
    $cadena = str_ireplace("]", "", $cadena);
    $cadena = str_ireplace("==", "", $cadena);
    $cadena = str_ireplace(";", "", $cadena);
    $cadena = str_ireplace("::", "", $cadena);
    $cadena = trim($cadena);
    $cadena = stripslashes($cadena);
    return $cadena;
}

// Tambien podemos hacer uso de la siguiente función

function sanitizing_filter($string)
{
    $pattern = '/(DROP TABLE|SELECT \* FROM|TRUNCATE TABLE|SHOW TABLES|\<|==|\<?php|\?|\/|--|\:|\;|script|\>|\^|\[|\]|DELETE FROM|INSERT INTO|DROP DATABASE|SHOW DATABASE)/i';
    $string =  preg_replace($pattern, '', $string);
    return  $string;
}

# Prueba de limpieza de filtro
// echo sanitizing_filter('<script>Hola mundo</script>');


# Funcion renombrar fotos #
function renombrar_fotos($nombre)
{
    $nombre = str_ireplace(" ", "_", $nombre);
    $nombre = str_ireplace("/", "_", $nombre);
    $nombre = str_ireplace("#", "_", $nombre);
    $nombre = str_ireplace("-", "_", $nombre);
    $nombre = str_ireplace("$", "_", $nombre);
    $nombre = str_ireplace(".", "_", $nombre);
    $nombre = str_ireplace(",", "_", $nombre);
    $nombre = $nombre . "_" . rand(0, 100);
    return $nombre;
}

# Prueba de limpieza de nombre de una foto
// $foto = 'Play Station 5/front-image.jpg';
// echo renombrar_fotos($foto);

# Funcion paginador de tablas #
function paginador_tablas($pagina, $Npaginas, $url, $botones)
{
    $tabla = '<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';

    if ($pagina <= 1) {
        $tabla .= '
        <a class="pagination-previous is-disabled" disabled >Anterior</a>
        <ul class="pagination-list">';
    } else {
        $tabla .= '
        <a class="pagination-previous" href="' . $url . ($pagina - 1) . '" >Anterior</a>
        <ul class="pagination-list">
            <li><a class="pagination-link" href="' . $url . '1">1</a></li>
            <li><span class="pagination-ellipsis">&hellip;</span></li>
        ';
    }

    $ci = 0;
    for ($i = $pagina; $i <= $Npaginas; $i++) {
        if ($ci >= $botones) {
            break;
        }
        if ($pagina == $i) {
            $tabla .= '<li><a class="pagination-link is-current" href="' . $url . $i . '">' . $i . '</a></li>';
        } else {
            $tabla .= '<li><a class="pagination-link" href="' . $url . $i . '">' . $i . '</a></li>';
        }
        $ci++;
    }

    if ($pagina == $Npaginas) {
        $tabla .= '
        </ul>
        <a class="pagination-next is-disabled" disabled >Siguiente</a>
        ';
    } else {
        $tabla .= '
            <li><span class="pagination-ellipsis">&hellip;</span></li>
            <li><a class="pagination-link" href="' . $url . $Npaginas . '">' . $Npaginas . '</a></li>
        </ul>
        <a class="pagination-next" href="' . $url . ($pagina + 1) . '" >Siguiente</a>
        ';
    }

    $tabla .= '</nav>';
    return $tabla;
}
