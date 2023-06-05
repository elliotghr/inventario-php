<?php
// Destruimos la session
session_destroy();
// Si se mandaron cabeceras js redireccionaremos con JS
if (headers_sent()) {
    echo "
        <script>
            location.href='index.php?vista=login';
        </script>
        ";
} else {
    // si no, generamos el redirect con PHP
    header("Location: index.php?vista=login");
}
