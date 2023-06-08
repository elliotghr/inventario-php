<?php

$id_producto = limpiar_cadena($_GET['product_id_del']);

$pdo = conexion();

// Primero verificamos la existencia del producto
$check_category = $pdo->query("SELECT foto  FROM productos WHERE producto_id = '$id_producto'");
// Si el producto existe...
if ($check_category->rowCount() == 1) {
    // Guardamos la imagen de la foto en esta variable para proceder a eliminarla si se puede elminar el registro
    $foto = $check_category->fetch(PDO::FETCH_ASSOC)['foto'];
    // Generamos el DELETE con consultas preparadas
    $del_product = $pdo->prepare("DELETE FROM productos WHERE producto_id = :id_producto");
    $del_product->bindParam(':id_producto', $id_producto);
    $del_product->execute();
    // Si la eliminación fue exitosa...
    if ($del_product->rowCount() == 1) {
        if (is_file("./img/productos/$foto")) {
            chmod("./img/productos/", 0777);
            // Eliminamos la imagen pasando la ruta
            unlink("./img/productos/$foto");
        }
        echo '
            <div class="notification is-info is-light">
                <button class="delete"></button>
                Producto eliminado con exito
            </div>
            ';
    } else {
        // Si No se pudo eliminar el producto
        echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                Ocurrió un error inesperado. No se pudo eliminar el producto.
            </div>
            ';
    }
} else {
    // El producto no existe
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Ocurrió un error inesperado. El producto no existe.
    </div>
    ';
}

$pdo = null;
