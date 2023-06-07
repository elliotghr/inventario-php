<?php

require_once "/xampp/htdocs/inventario/inc/session_start.php";
require_once "/xampp/htdocs/inventario/php/main.php";

$id = limpiar_cadena($_POST['categoria_id']);

//  Verificamos el usuario
$pdo = conexion();
$get_category = $pdo->query("SELECT * FROM categorias WHERE categoria_id = '$id'");

// Si el usuario no existe retornamos el mensaje de error
if ($get_category->rowCount() <= 0) {
    echo '
    <div class="notification is-danger is-light mb-6 mt-6">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        No podemos obtener la información solicitada
    </div>
    ';
    $pdo = null;
    return;
}
// Si sí existe seguiremos con las validaciones y actualización

// Guardamos los datos de la DB
$datos = $get_category->fetch(PDO::FETCH_ASSOC);
$pdo = null;

// Almacenamos los datos
$categoria_nombre = limpiar_cadena($_POST['categoria_nombre']);
$categoria_ubicacion = limpiar_cadena($_POST['categoria_ubicacion']);


// VERIFICACIÓN DE DATOS
# verificamos los campos obligatorios
if ($categoria_nombre == '') {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        No has llenado los campos obligatorios que corresponden al Nombre
    </div>
    ';
    exit();
}

# VERIFICACIÓN DE INTEGRIDAD DE DATOS
if (verificar_datos('[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}', $categoria_nombre)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El nombre no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if ($categoria_ubicacion) {
    if (verificar_datos('[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}', $categoria_ubicacion)) {
        echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            La ubicación no coincide con el formato solicitado
        </div>
    ';
        exit();
    };
}

// VALIDACIÓN NOMBRE
// Si el nombre que ingresaron es diferente al que teníamos en nuestra DB...
if ($categoria_nombre != $datos['nombre']) {
    $pdo = conexion();
    $check_nombre = $pdo->query("SELECT nombre FROM categorias WHERE nombre='$categoria_nombre'");
    // Si existe saldremos del programa
    if ($check_nombre->rowCount() >= 1) {
        echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                El nombre de la categoría ya existe en nuestros registros, por favor, elija otro.
            </div>
        ';
        exit();
    }
    // Si no existe todo está correcto, el usuario es válido para actualizarlo y cerramos la conexión a nuestra DB
    $pdo = null;
}


// ACTUALIZACIÓN DE DATOS
$pdo = conexion();

$category_update = $pdo->prepare("UPDATE categorias SET nombre=:nombre, ubicacion=:ubicacion WHERE categoria_id =:categoria_id");

$category_update->bindParam(':categoria_id', $id);
$category_update->bindParam(':nombre', $categoria_nombre);
$category_update->bindParam(':ubicacion', $categoria_ubicacion);

if ($category_update->execute()) {
    echo '
        <div class="notification is-info">
        <button class="delete"></button>
        Categoría actualizada con éxito
        </div>
    ';
    exit();
} else {
    echo '
        <div class="notification is-danger">
        <button class="delete"></button>
        Ha ocurrido un error al actualizar la categoría
        </div>
    ';
    exit();
}
$pdo = null;
