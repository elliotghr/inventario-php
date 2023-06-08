<?php
require_once "../php/main.php";

/*== Almacenando id del producto ==*/
$product_id = limpiar_cadena($_POST['img_up_id']);

/*== Verificando producto ==*/
$pdo = conexion();
$check_producto = $pdo->query("SELECT * FROM productos WHERE producto_id='$product_id'");

// Comprobamos la existencia del producto
if ($check_producto->rowCount() == 1) {
    $datos = $check_producto->fetch(PDO::FETCH_ASSOC);
} else {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La imagen del PRODUCTO que intenta actualizar no existe
            </div>
        ';
    exit();
}
$check_producto = null;


/*== Comprobando si se ha seleccionado una imagen ==*/
if ($_FILES['foto']['name'] == "" || $_FILES['foto']['size'] == 0) {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No ha seleccionado ninguna imagen o foto
            </div>
        ';
    exit();
}


/* Directorios de imagenes */
$img_dir = '../img/productos/';


/* Creando directorio de imagenes */
if (!file_exists($img_dir)) {
    if (!mkdir($img_dir, 0777)) {
        echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    Error al crear el directorio de imagenes
                </div>
            ';
        exit();
    }
}

/* Cambiando permisos al directorio */
chmod($img_dir, 0777);

/* Comprobando formato de las imagenes */
if (mime_content_type($_FILES['foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['foto']['tmp_name']) != "image/png") {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La imagen que ha seleccionado es de un formato que no está permitido
            </div>
        ';
    exit();
}


/* Comprobando que la imagen no supere el peso permitido */
if (($_FILES['foto']['size'] / 1024) > 3072) {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La imagen que ha seleccionado supera el límite de peso permitido
            </div>
        ';
    exit();
}


/* obteniendo la extensión de la imagen */
switch (mime_content_type($_FILES['foto']['tmp_name'])) {
    case 'image/jpg':
        $img_ext = ".jpg";
        break;
    case 'image/png':
        $img_ext = ".png";
        break;
    case 'image/jpeg':
        $img_ext = ".jpeg";
        break;
}

/* Nombre de la imagen */
$img_nombre = renombrar_fotos($datos['nombre']);

/* Nombre final de la imagen con extensión */
$foto = $img_nombre . $img_ext;

/* Moviendo imagen al directorio */
// Si falla salimos de la ejecución del programa
if (!move_uploaded_file($_FILES['foto']['tmp_name'], $img_dir . $foto)) {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No podemos subir la imagen al sistema en este momento, por favor intente nuevamente
            </div>
        ';
    exit();
}


/* Eliminando la imagen anterior */
if (is_file($img_dir . $datos['foto']) && $datos['foto'] != $foto) {

    chmod($img_dir . $datos['foto'], 0777);
    // unlink para eliminar la foto
    unlink($img_dir . $datos['foto']);
}

/*== Actualizando datos ==*/
$actualizar_producto = conexion();
// Generamos la consulta preparada
$actualizar_producto = $actualizar_producto->prepare("UPDATE productos SET foto=:foto WHERE producto_id=:id");

$marcadores = [
    ":foto" => $foto,
    ":id" => $product_id
];

// Si la actualización fue exitosa mandamos el mensaje de exito y un botón para recargar la pagina
if ($actualizar_producto->execute($marcadores)) {
    echo '
            <div class="notification is-info is-light">
                <strong>¡IMAGEN O FOTO ACTUALIZADA!</strong><br>
                La imagen del producto ha sido actualizada exitosamente, pulse Aceptar para recargar los cambios.

                <p class="has-text-centered pt-5 pb-5">
                    <a href="index.php?vista=product_img&product_id_up=' . $product_id . '" class="button is-link is-rounded">Aceptar</a>
                </p">
            </div>
        ';
} else {
    // Si no se pudo actualizar, entonces, eliminamos la foto y mandamos el mensaje de error
    if (is_file($img_dir . $foto)) {
        chmod($img_dir . $foto, 0777);
        unlink($img_dir . $foto);
    }

    echo '
            <div class="notification is-warning is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No podemos subir la imagen al sistema en este momento, por favor intente nuevamente
            </div>
        ';
}

$pdo = null;
