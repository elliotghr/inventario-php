<?php
$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

// Campos del select
$campos = "productos.producto_id,productos.codigo,productos.nombre as producto_nombre,productos.precio,productos.stock,productos.foto,productos.categoria_id,productos.usuario_id,categorias.categoria_id,categorias.nombre,usuarios.usuario_id,usuarios.nombre,usuarios.apellido";

// Si hay una busqueda
if (isset($busqueda) && $busqueda != "") {
    $consulta = "SELECT SQL_CALC_FOUND_ROWS $campos FROM productos INNER JOIN categorias ON productos.categoria_id=categorias.categoria_id INNER JOIN usuarios ON productos.usuario_id=usuarios.usuario_id WHERE productos.codigo LIKE '%$busqueda%' OR productos.nombre LIKE '%$busqueda%' ORDER BY productos.nombre ASC LIMIT $inicio,$registros";
} elseif ($categoria_id > 0) {
    // Si se está buscando por categoría vamos a buscar x el id de esa categoria
    $consulta = "SELECT SQL_CALC_FOUND_ROWS $campos FROM productos INNER JOIN categorias ON productos.categoria_id=categorias.categoria_id INNER JOIN usuarios ON productos.usuario_id=usuarios.usuario_id WHERE productos.categoria_id='$categoria_id' ORDER BY productos.nombre ASC LIMIT $inicio,$registros";
} else {
    // Select x defecto
    $consulta = "SELECT SQL_CALC_FOUND_ROWS $campos FROM productos INNER JOIN categorias ON productos.categoria_id=categorias.categoria_id INNER JOIN usuarios ON productos.usuario_id=usuarios.usuario_id ORDER BY productos.nombre ASC LIMIT $inicio,$registros";
}

$conexion = conexion();
// Ejecutamos la consulta
$datos = $conexion->query($consulta);

// Guardamos datos
$datos = $datos->fetchAll(PDO::FETCH_ASSOC);

// Traemos el total de nuestros registros
$total = $conexion->query("SELECT FOUND_ROWS()");
$total = (int) $total->fetchColumn();

$Npaginas = ceil($total / $registros);

if ($total >= 1 && $pagina <= $Npaginas) {
    $contador = $inicio + 1;
    $pag_inicio = $inicio + 1;
    foreach ($datos as $rows) {
        $tabla .= '
				<article class="media">
			        <figure class="media-left">
			            <p class="image is-64x64">';
        if (is_file("./img/productos/" . $rows['foto'])) {
            // Verificamos si podemos obtener la imagen con is_file, en caso de exito la renderizamos
            $tabla .= '<img src="./img/productos/' . $rows['foto'] . '">';
        } else {
            // en caso de que no exista o haya sido null mandamos una imagen x defecto
            $tabla .= '<img src="./img/producto.png">';
        }
        // En esta sección renderizamos todos los datos de nuestros productos
        $tabla .= '</p>
			        </figure>
			        <div class="media-content">
			            <div class="content">
			              <p>
			                <strong>' . $contador . ' - ' . $rows['nombre'] . '</strong><br>
			                <strong>CODIGO:</strong> ' . $rows['codigo'] . ', <strong>PRECIO:</strong> $' . $rows['precio'] . ', <strong>STOCK:</strong> ' . $rows['stock'] . ', <strong>CATEGORIA:</strong> ' . $rows['producto_nombre'] . ', <strong>REGISTRADO POR:</strong> ' . $rows['nombre'] . ' ' . $rows['apellido'] . '
			              </p>
			            </div>
			            <div class="has-text-right">
			                <a href="index.php?vista=product_img&product_id_up=' . $rows['producto_id'] . '" class="button is-link is-rounded is-small">Imagen</a>
			                <a href="index.php?vista=product_update&product_id_up=' . $rows['producto_id'] . '" class="button is-success is-rounded is-small">Actualizar</a>
			                <a href="' . $url . $pagina . '&product_id_del=' . $rows['producto_id'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
			            </div>
			        </div>
			    </article>

			    <hr>
            ';
        $contador++;
    }
    $pag_final = $contador - 1;
} else {
    // Si me paso de pagina evalúp si estoy dentro del total
    if ($total >= 1) {
        $tabla .= '
				<p class="has-text-centered" >
					<a href="' . $url . '1" class="button is-link is-rounded is-small mt-4 mb-4">
						Haga clic acá para recargar el listado
					</a>
				</p>
			';
    } else {
        $tabla .= '
				<p class="has-text-centered" >No hay registros en el sistema</p>
			';
    }
}

if ($total > 0 && $pagina <= $Npaginas) {
    $tabla .= '<p class="has-text-right">Mostrando productos <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';
}

$conexion = null;
echo $tabla;

if ($total >= 1 && $pagina <= $Npaginas) {
    echo paginador_tablas($pagina, $Npaginas, $url, 7);
}
