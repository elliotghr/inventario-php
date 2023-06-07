<?php
// Obtengo los datos de la session
include "../inc/session_start.php";
include "main.php";

// RECEPCIÓN DE DATOS
# recibimos los datos del fetch y los limpiamos
$producto_codigo = limpiar_cadena($_POST['producto_codigo']);
$producto_nombre = limpiar_cadena($_POST['producto_nombre']);
$producto_precio = limpiar_cadena($_POST['producto_precio']);
$producto_stock = limpiar_cadena($_POST['producto_stock']);
$producto_categoria = limpiar_cadena($_POST['producto_categoria']);
$foto = "";

// VERIFICACIÓN DE DATOS
# verificamos los campos obligatorios
if ($producto_codigo == '' || $producto_nombre == '' || $producto_precio == '' || $producto_stock == '' || $producto_categoria == '') {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            No has llenado todos los campos obligatorios
        </div>
    ';
    exit();
}

# VERIFICACIÓN DE INTEGRIDAD DE DATOS
if (verificar_datos('[a-zA-Z0-9- ]{1,70}', $producto_codigo)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El código no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if (verificar_datos('[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}', $producto_nombre)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El nombre no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if (verificar_datos('[0-9.]{1,25}', $producto_precio)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El precio no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if (verificar_datos('[0-9.]{1,25}', $producto_stock)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El numero de stock no coincide con el formato solicitado
        </div>
    ';
    exit();
};


// VERIFICAMOS DUPLICIDAD DE CAMPOS EN LA DB
// Verificamos el código de barras
$pdo = conexion();
// Lanzamos la consulta
$check_codigo = $pdo->query("SELECT codigo FROM productos WHERE codigo = '$producto_codigo'");
// Contamos el número de registros
if ($check_codigo->rowCount()) {
    echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                El código de barras ya existe, ingrese otro.
            </div>
        ';
    // Cerramos la conexión
    $pdo = null;
    exit();
}
// Verificamos el nombre del producto
// Lanzamos la consulta
$check_nombre = $pdo->query("SELECT nombre FROM productos WHERE nombre = '$producto_nombre'");
// Contamos el número de registros
if ($check_nombre->rowCount()) {
    echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                El nombre del producto ya existe, ingrese otro.
            </div>
        ';
    // Cerramos la conexión
    $pdo = null;
    exit();
}

// VERIFICACIÓN DE EXISTENCIA DE CATEGORÍA
// Verificamos la categoria
// Lanzamos la consulta
$check_categoria = $pdo->prepare("SELECT * FROM categorias WHERE categoria_id = :id_category");
$check_categoria->bindParam(":id_category", $producto_categoria);
// Contamos el número de registros
if (!$check_categoria->execute()) {
    echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                La categoria no existe, intente de nuevo.
            </div>
        ';
    // Cerramos la conexión
    $pdo = null;
    exit();
}

// DIRECTORIO DE IMAGENES

// Definimos la ruta de las imagenes
$img_dir = "../img/productos/";
// Validamos si viene una foto, ya que no es un campo requerido
if ($_FILES['producto_foto']['name'] != "") {
    // Verificando el directorio
    if (!file_exists($img_dir)) {
        // Si no existe intentaremos crear el directorio
        if (!mkdir($img_dir, 0777)) {
            // En caso de error al crear el directorio mandaremos un mensaje
            echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                No se pudo crear el directorio, intente de nuevo.
            </div>
            ';
            exit();
        }
    }
    // Si todo va correcto verificamos el formato de las imagenes: jpg,jpeg,png
    if (mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/jpg" && mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/png" && mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/jpeg") {
        echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El formato del archivo no es valido
        </div>
        ';
        exit();
    }
    // Comparamos el tamaño del archivo
    // Convertimos a kB
    if ($_FILES['producto_foto']['size'] / 1024 > 3072) {
        echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            La imagen supera el tamaño especificado, ingrese otra imagen.
        </div>
        ';
        exit();
    }
    // Obtenemos la extensión de la imagen
    switch (mime_content_type($_FILES['producto_foto']['tmp_name'])) {
        case 'image/jpg':
            $img_ext = ".jpg";
            break;
        case 'image/png':
            $img_ext = ".png";
            break;
        case 'image/jpeg':
            $img_ext = ".jpeg";
            break;

        default:
            $img_ext = ".undefined";
            break;
    }

    // Damos permisos a la carpeta de las imagenes
    chmod($img_dir, 0777);

    /* Nombre de la imagen */
    $img_nombre = renombrar_fotos($producto_nombre);

    // Definimos el nombre final de la imagen con el nombre del producto, concatenando la extensión obtenida
    $foto = $img_nombre . $img_ext;

    // Movemos la imagen a nuestra carpeta
    if (!move_uploaded_file($_FILES['producto_foto']['tmp_name'], $img_dir . $foto)) {
        echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            Ha ocurrido un error al guardar la imagen, intente mas tarde.
        </div>
        ';
        exit();
    }
    // Si termina el script tenemos la imagen almacenada y la variable $foto para insertar el nombre del archivo en la DB
} else {
    $foto = "";
}
$pdo = conexion();

$insert = $pdo->prepare("INSERT INTO productos (codigo,nombre,precio,stock,foto,categoria_id,usuario_id) VALUES (:codigo,:nombre,:precio,:stock,:foto,:categoria_id,:usuario_id)");

$insert->bindParam(":codigo", $producto_codigo);
$insert->bindParam(":nombre", $producto_nombre);
$insert->bindParam(":precio", $producto_precio);
$insert->bindParam(":stock", $producto_stock);
$insert->bindParam(":foto", $foto);
$insert->bindParam(":categoria_id", $producto_categoria);
$insert->bindParam(":usuario_id", $_SESSION['id']);

$insert->execute();

if ($insert->rowCount() > 0) {
    echo '
    <div class="notification is-info">
        <button class="delete"></button>
        ¡Producto creado con éxito!
    </div>
    ';
    exit();
} else {
    // Si no se pudo insertar el producto eliminaremos la foto recibida y mendaremos un mensaje de error al insertar
    if (is_file($img_dir . $foto)) {
        chmod($img_dir, 0777);
        // Eliminamos la imagen pasando la ruta
        unlink($img_dir . $foto);
    }
    // mensaje de error 
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Ha ocurrido un error al guardar el producto, intente mas tarde.
    </div>
    ';
    exit();
}
