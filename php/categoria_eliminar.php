<?php

$id_category = limpiar_cadena($_GET['category_id_del']);

$pdo = conexion();

// Primero verificamos la existencia del usuario
$check_category = $pdo->query("SELECT categoria_id FROM categorias WHERE categoria_id = '$id_category'");

// Si el usuario existe, comprobaremos si se puede eliminar
if ($check_category->rowCount() == 1) {
    // verificamos los productos registrados por el usuario (su FK)
    $check_productos = $pdo->query("SELECT categoria_id FROM productos WHERE categoria_id = '$id_category'");

    // Si no tiene productos lo eliminaremos
    if ($check_productos->rowCount() <= 0) {
        // Generamos el DELETE con consultas preparadas
        $del_user = $pdo->prepare("DELETE FROM categorias WHERE categoria_id = :id_category");
        $del_user->bindParam(':id_category', $id_category);
        $del_user->execute();
        // Si la eliminación fue exitosa...
        if ($del_user->rowCount() == 1) {
            echo '
            <div class="notification is-info is-light">
                <button class="delete"></button>
                Categoría eliminada con exito
            </div>
            ';
        } else {
            // Si No se pudo eliminar el usuario
            echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                Ocurrió un error inesperado. No se pudo eliminar la categoría.
            </div>
            ';
        }
    } else {
        // Si tiene productos no lo haremos (Debido a nuestras restricciones)
        echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            No se puede eliminar la categoria porque tiene productos asignados
        </div>
        ';
    }
} else {
    // El usuario no existe
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Ocurrió un error inesperado. La categoría no existe.
    </div>
    ';
}

$pdo = null;
