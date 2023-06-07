<?php
// obtenemos las funciones del main
require_once "/xampp/htdocs/inventario/inc/session_start.php";
require_once "/xampp/htdocs/inventario/php/main.php";

// RECEPCIÓN DE DATOS
# recibimos los datos del fetch
$categoria_nombre = limpiar_cadena($_POST['categoria_nombre']);
$categoria_ubicacion = limpiar_cadena($_POST['categoria_ubicacion']);

// VERIFICACIÓN DE DATOS
# verificamos los campos obligatorios
// Solo verificamos el nombre, la ubicación sí puede ir vacía
if ($categoria_nombre == '') {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Ha ocurrido un error inesperado
    </div>
    ';
    exit();
}

# VERIFICACIÓN DE INTEGRIDAD DE DATOS
// Preferible con JS
if (verificar_datos('[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}', $categoria_nombre)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El nombre no coincide con el formato solicitado
        </div>
    ';
    exit();
};
// Cuando tenga texto haremos la verificación
if ($categoria_ubicacion != "") {
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

// Verificamos que el nombre de la categoría no exista
// Creamos la conexión
$pdo = conexion();
// Lanzamos la consulta
$check_category = $pdo->query("SELECT nombre FROM categorias WHERE nombre = '$categoria_nombre'");
// Contamos el número de registros
if ($check_category->rowCount()) {
    echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                La categoría ya existe, por favor elija otro.
            </div>
        ';
    // Cerramos la conexión
    $pdo = null;
    exit();
}


# INSERTANDO/GUARDANDO DATOS

$user_insert = $pdo->prepare("INSERT INTO categorias (nombre,ubicacion) VALUES (:categoria_nombre,:categoria_ubicacion)");
$user_insert->bindParam(':categoria_nombre', $categoria_nombre);
$user_insert->bindParam(':categoria_ubicacion', $categoria_ubicacion);
// Si se insertó nuestro registro...
if ($user_insert->execute()) {
    echo '
        <div class="notification is-info">
            <button class="delete"></button>
            Categoría registrada!
        </div>
    ';
} else {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            Error al insertar el registro, intente de nuevo.
        </div>
    ';
    // Cerramos la conexión
    $pdo = null;
    exit();
}

// Cerramos la conexión
$pdo = null;
