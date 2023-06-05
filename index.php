<?php
require_once "./inc/session_start.php"
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php
    require_once "./inc/head.php"
    ?>
</head>

<body>
    <?php
    // comprobamos si por la url viene la variable vista, si no existe o su valor es vacío le asignamos el valor 'login'
    if (!isset($_GET['vista']) || $_GET['vista'] == "") $_GET['vista'] = 'login';

    // Si el valor de $_GET['vista'] coincide con algún archivo que no sea login o 404 accedemos a la vista
    if (is_file("./vistas/" . $_GET['vista'] . ".php") && $_GET['vista'] != 'login' && $_GET['vista'] != '404') {
        // Cerrar sesión forzadamente
        if ((!isset($_SESSION['id']) || ($_SESSION['id'] == "")) || (!isset($_SESSION['usuario']) || ($_SESSION['usuario'] == ""))) {
            include("./vistas/logout.php");
            exit();
        }
        require_once "./inc/navbar.php";
        require_once "./vistas/" . $_GET['vista'] . ".php";
        require_once "./inc/script.php";
    } else {
        // En caso de que el valor de $_GET['vista'] no coincida verificamos si el valor es 'login' para que se autentique o enviamos un error 404
        if ($_GET['vista'] == 'login') {
            // require_once "./inc/script.php";
            require_once "./vistas/login.php";
        } else {
            require_once "./vistas/404.php";
        }
    }
    ?>
</body>

</html>