<?php
// Generar el inicio del LIMIT
$inicio = $pagina > 0 ? (($pagina * $registros) - $registros) : 0;
$table = "";

// GENERAMOS LAS CONSULTAS PARA TRAER TODOS LOS REGISTROS O LOS REGISTROS DE UNA BUSQUEDA
if (isset($busqueda) && $busqueda != "") {
    // Consulta para el buscador
    $consulta_datos = "SELECT * FROM usuarios WHERE ((usuario_id != " . $_SESSION['id'] . ") AND (nombre LIKE '%$busqueda%') OR (apellido LIKE '%$busqueda%') OR (usuario LIKE '%$busqueda%') OR (email LIKE '%$busqueda%') ) ORDER BY nombre ASC LIMIT $inicio, $registros;";

    // Consulta para el total del buscador
    $consulta_total = "SELECT COUNT(*) FROM usuarios WHERE ((usuario_id != " . $_SESSION['id'] . ") AND (nombre LIKE '%$busqueda%') OR (apellido LIKE '%$busqueda%') OR (usuario LIKE '%$busqueda%') OR (email LIKE '%$busqueda%'))";
} else {
    // Traemos todos los registros ordenados ascendentemente sin traer el usuario mismo
    $consulta_datos = "SELECT * FROM usuarios WHERE usuario_id != " . $_SESSION['id'] . " ORDER BY nombre ASC LIMIT $inicio, $registros";

    // Traemos el conteo de todos los registros sin traer el usuario mismo
    $consulta_total = "SELECT COUNT(*) FROM usuarios WHERE usuario_id != " . $_SESSION['id'] . "";
}

$pdo  = conexion();
$get_data = $pdo->query($consulta_datos);
$data = $get_data->fetchAll(PDO::FETCH_ASSOC);

$get_total = $pdo->query($consulta_total);
$total = $get_total->fetchColumn();
// Cálculo de paginas
$n_paginas = ceil($total / $registros);

// Generamos la tabla
$table .= '
<div class="table-container">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr class="has-text-centered">
                    <th>#</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th colspan="2">Opciones</th>
                </tr>
            </thead>
            <tbody>
';

if ($total >= 1 && $pagina <= $n_paginas) {
    $contador = $inicio + 1;
    $pag_inicio = $inicio + 1;
    foreach ($data as $key => $row) {
        $table .= '
        <tr class="has-text-centered">
            <td>' . $contador . '</td>
            <td>' . $row['nombre'] . '</td>
            <td>' . $row['apellido'] . '</td>
            <td>' . $row['usuario'] . '</td>
            <td>' . $row['email'] . '</td>
            <td>
                <a href="index.php?vista=user_update&user_id_up=' . $row['usuario_id'] . '" class="button is-success is-rounded is-small">Actualizar</a>
            </td>
            <td>
                <a href="' . $url . $pagina . '&user_id_del=' . $row['usuario_id'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
            </td>
        </tr>
        ';
        $contador++;
    }
    $pag_final = $contador - 1;
} else {
    if ($total >= 1) {
        $table .= '
        <tr class="has-text-centered">
            <td colspan="7">
                <a href="' . $url . '1" class="button is-link is-rounded is-small mt-4 mb-4">
                    Haga clic acá para recargar el listado
                </a>
            </td>
        </tr>
        ';
    } else {
        $table .= '
        <tr class="has-text-centered">
            <td colspan="7">
                No hay registros en el sistema
            </td>
        </tr>
        ';
    }
}
$table .= '
        </tbody>
    </table>
</div>
';


// Añadimos a la variable table la leyenda de Mostrando usuarios n - n...
if ($total >= 1 && $pagina <= $n_paginas) {
    $table .= '
    <p class="has-text-right">Mostrando usuarios <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>
    ';
}

// Cerramos la sesión
$pdo = null;

echo $table;

if ($total >= 1 && $pagina <= $n_paginas) {
    echo paginador_tablas($pagina, $n_paginas, $url, 7);
}
