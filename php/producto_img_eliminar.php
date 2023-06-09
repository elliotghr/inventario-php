<?php
require_once "../php/main.php";

/*== Almacenando datos ==*/
$product_id = limpiar_cadena($_POST['img_del_id']);

/*== Verificando producto ==*/
$pdo = conexion();
$check_producto = $pdo->query("SELECT * FROM productos WHERE producto_id='$product_id'");

if ($check_producto->rowCount() == 1) {
    $datos = $check_producto->fetch();
} else {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La imagen del PRODUCTO que intenta eliminar no existe
            </div>
        ';
    exit();
}
$pdo = null;


/* Directorios de imagenes */
$img_dir = '../img/productos/';

/* Cambiando permisos al directorio */
chmod($img_dir, 0777);


/* Eliminando la imagen del directorio */
if (is_file($img_dir . $datos['foto'])) {

    chmod($img_dir . $datos['foto'], 0777);

    if (!unlink($img_dir . $datos['foto'])) {
        echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                Error al intentar eliminar la imagen del producto, por favor intente nuevamente
	            </div>
	        ';
        exit();
    }
}


/*== Actualizando datos ==*/
$pdo = conexion();
$actualizar_producto = $pdo->prepare("UPDATE productos SET foto=:foto WHERE producto_id=:id");

$marcadores = [
    ":foto" => "",
    ":id" => $product_id
];

if ($actualizar_producto->execute($marcadores)) {
    echo '
            <div class="notification is-info is-light">
                <strong>¡IMAGEN O FOTO ELIMINADA!</strong><br>
                La imagen del producto ha sido eliminada exitosamente, pulse Aceptar para recargar los cambios.

                <p class="has-text-centered pt-5 pb-5">
                    <a href="index.php?vista=product_img&product_id_up=' . $product_id . '" class="button is-link is-rounded">Aceptar</a>
                </p">
            </div>
        ';
} else {
    echo '
            <div class="notification is-warning is-light">
                <strong>¡IMAGEN O FOTO ELIMINADA!</strong><br>
                Ocurrieron algunos inconvenientes, sin embargo la imagen del producto ha sido eliminada, pulse Aceptar para recargar los cambios.

                <p class="has-text-centered pt-5 pb-5">
                    <a href="index.php?vista=product_img&product_id_up=' . $product_id . '" class="button is-link is-rounded">Aceptar</a>
                </p">
            </div>
        ';
}
$pdo = null;
