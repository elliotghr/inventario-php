<?php
// iMPORTO FUNCIONES
require_once "../php/main.php";

/*== Almacenando id ==*/
$id = limpiar_cadena($_POST['producto_id']);


/*== Verificando producto ==*/
$check_producto = conexion();
$check_producto = $check_producto->query("SELECT * FROM productos WHERE producto_id='$id'");

if ($check_producto->rowCount() <= 0) {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El producto no existe en el sistema
            </div>
        ';
    exit();
} else {
    $datos = $check_producto->fetch(PDO::FETCH_ASSOC);
}
$check_producto = null;


/*== Almacenando datos ==*/
$codigo = limpiar_cadena($_POST['producto_codigo']);
$nombre = limpiar_cadena($_POST['producto_nombre']);

$precio = limpiar_cadena($_POST['producto_precio']);
$stock = limpiar_cadena($_POST['producto_stock']);
$categoria = limpiar_cadena($_POST['producto_categoria']);


/*== Verificando campos obligatorios ==*/
if ($codigo == "" || $nombre == "" || $precio == "" || $stock == "" || $categoria == "") {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No has llenado todos los campos que son obligatorios
            </div>
        ';
    exit();
}


/*== Verificando integridad de los datos ==*/
if (verificar_datos("[a-zA-Z0-9- ]{1,70}", $codigo)) {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El CODIGO de BARRAS no coincide con el formato solicitado
            </div>
        ';
    exit();
}

if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}", $nombre)) {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
    exit();
}

if (verificar_datos("[0-9.]{1,25}", $precio)) {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El PRECIO no coincide con el formato solicitado
            </div>
        ';
    exit();
}

if (verificar_datos("[0-9]{1,25}", $stock)) {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                El STOCK no coincide con el formato solicitado
            </div>
        ';
    exit();
}


/*== Verificando la exitencia del codigo para evitar duplicados==*/
if ($codigo != $datos['codigo']) {
    $check_codigo = conexion();
    $check_codigo = $check_codigo->query("SELECT codigo FROM productos WHERE codigo='$codigo'");
    if ($check_codigo->rowCount() > 0) {
        echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                El CODIGO de BARRAS ingresado ya se encuentra registrado, por favor elija otro
	            </div>
	        ';
        exit();
    }
    $check_codigo = null;
}


/*== Verificando la exitencia del nombre para evitar duplicados ==*/
if ($nombre != $datos['nombre']) {
    $check_nombre = conexion();
    $check_nombre = $check_nombre->query("SELECT nombre FROM productos WHERE nombre='$nombre'");
    if ($check_nombre->rowCount() > 0) {
        echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                El NOMBRE ingresado ya se encuentra registrado, por favor elija otro
	            </div>
	        ';
        exit();
    }
    $check_nombre = null;
}


/*== Verificando la exitencia de la categoria ==*/
if ($categoria != $datos['categoria_id']) {
    $check_categoria = conexion();
    $check_categoria = $check_categoria->query("SELECT categoria_id FROM categorias WHERE categoria_id='$categoria'");
    if ($check_categoria->rowCount() <= 0) {
        echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                La categoría seleccionada no existe
	            </div>
	        ';
        exit();
    }
    $check_categoria = null;
}


/*== Actualizando datos ==*/
$pdo = conexion();
$actualizar_producto = $pdo->prepare("UPDATE productos SET codigo=:codigo,nombre=:nombre,precio=:precio,stock=:stock,categoria_id=:categoria WHERE producto_id=:id");

$marcadores = [
    ":codigo" => $codigo,
    ":nombre" => $nombre,
    ":precio" => $precio,
    ":stock" => $stock,
    ":categoria" => $categoria,
    ":id" => $id
];


if ($actualizar_producto->execute($marcadores)) {
    echo '
            <div class="notification is-info is-light">
                <strong>¡PRODUCTO ACTUALIZADO!</strong><br>
                El producto se actualizo con exito
            </div>
        ';
} else {
    echo '
            <div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo actualizar el producto, por favor intente nuevamente
            </div>
        ';
}
$pdo = null;
