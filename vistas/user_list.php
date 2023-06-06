<div class="container is-fluid mb-6">
    <h1 class="title">Usuarios</h1>
    <h2 class="subtitle">Lista de usuarios</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
    include "./php/main.php";
    // Comprobaciones de paginación
    if (!isset($_GET['page'])) {
        $pagina = 1;
    } else {
        $pagina = (int)$_GET['page'];
        if ($pagina <= 1) {
            $pagina = 1;
        }
    }
    $pagina = limpiar_cadena($pagina);
    // url a modificar en cada cambio de pagina
    $url = "index.php?vista=user_list&page=";
    // Variable limit
    $registros = 15;
    // Variable para la busqueda de registros
    $busqueda = "";

    // Table
    include "./php/usuario_lista.php";
    ?>

</div>