<?php

$id_user = limpiar_cadena($_GET['user_id_del']);

$pdo = conexion();

// Primero verificamos la existencia del usuario
$check_user = $pdo->query("SELECT usuario_id FROM usuarios WHERE usuario_id = '$id_user'");

// Si el usuario existe, comprobaremos si se puede eliminar
if ($check_user->rowCount() == 1) {
    // verificamos los productos registrados por el usuario (su FK)
    $check_productos = $pdo->query("SELECT usuario_id FROM productos WHERE usuario_id = '$id_user'");

    // Si no tiene productos lo eliminaremos
    if ($check_productos->rowCount() <= 0) {
        // Generamos el DELETE con consultas preparadas
        $del_user = $pdo->prepare("DELETE FROM usuarios WHERE usuario_id = :id_user");
        $del_user->bindParam(':id_user', $id_user);
        $del_user->execute();
        // Si la eliminación fue exitosa...
        if ($del_user->rowCount() == 1) {
            echo '
            <div class="notification is-info is-light">
                <button class="delete"></button>
                Usuario eliminado con exito
            </div>
            ';
        } else {
            // Si No se pudo eliminar el usuario
            echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                Ocurrió un error inesperado. No se pudo eliminar el usuario.
            </div>
            ';
        }
    } else {
        // Si tiene productos no lo haremos (Debido a nuestras restricciones)
        echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            No se puede eliminar el usuario porque tiene productos reigistrados
        </div>
        ';
    }
} else {
    // El usuario no existe
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Ocurrió un error inesperado. El usuario no existe.
    </div>
    ';
}



$pdo = null;
