<?php

require_once "/xampp/htdocs/inventario/inc/session_start.php";
require_once "/xampp/htdocs/inventario/php/main.php";

$id = limpiar_cadena($_POST['usuario_id']);

//  Verificamos el usuario
$pdo = conexion();
$get_user = $pdo->query("SELECT * FROM usuarios WHERE usuario_id = '$id'");

// Si el usuario no existe retornamos el mensaje de error
if ($get_user->rowCount() <= 0) {
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
$datos = $get_user->fetch(PDO::FETCH_ASSOC);
$pdo = null;

// Gurdamos los datos del admin para comprobar la actulización
$administrador_usuario = limpiar_cadena($_POST['administrador_usuario']);
$administrador_clave = limpiar_cadena($_POST['administrador_clave']);

// VERIFICACIÓN DE DATOS
# verificamos los campos obligatorios
if ($administrador_usuario == '' || $administrador_clave == '') {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        No has llenado los campos obligatorios que corresponden al Usuario y Clave
    </div>
    ';
    exit();
}

# VERIFICACIÓN DE INTEGRIDAD DE DATOS
if (verificar_datos('[a-zA-Z0-9]{4,20}', $administrador_usuario)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            Su usuario no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if (verificar_datos('[a-zA-Z0-9$@.-]{7,100}', $administrador_clave)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            La clave no coincide con el formato solicitado
        </div>
    ';
    exit();
};

// Verificamos los datos del admin y su session
$pdo = conexion();
$check_admin = $pdo->query("SELECT usuario, password FROM usuarios WHERE usuario = '$administrador_usuario' AND usuario_id = '" . $_SESSION['id'] . "'");

// Si el usuario no coincide
if ($check_admin->rowCount() <= 0) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            Usuario o clave de administrador incorrectos
        </div>
        ';
    exit();
}

// Si el usuario es el mismo entre session y el valor en el campo Usuario...
$check_admin = $check_admin->fetch(PDO::FETCH_ASSOC);

// Comparamos los datos de Usuario y Clave vs la DB
$pass_verify = password_verify($administrador_clave, $check_admin['password']);
// Si no coincide lo botamos
if ($check_admin['usuario'] != $administrador_usuario || !$pass_verify) {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Usuario o clave de administrador incorrectos.
    </div>
    ';
    exit();
}

// Almacenamos los datos
$usuario_nombre = limpiar_cadena($_POST['usuario_nombre']);
$usuario_apellido = limpiar_cadena($_POST['usuario_apellido']);
$usuario_usuario = limpiar_cadena($_POST['usuario_usuario']);
$usuario_email = limpiar_cadena($_POST['usuario_email']);
$usuario_clave_1 = limpiar_cadena($_POST['usuario_clave_1']);
$usuario_clave_2 = limpiar_cadena($_POST['usuario_clave_2']);


// VERIFICACIÓN DE DATOS
# verificamos los campos obligatorios
if ($usuario_nombre == '' || $usuario_apellido == '' || $usuario_usuario == '') {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        No has llenado los campos obligatorios que corresponden al Nombre, apellido o usuario
    </div>
    ';
    exit();
}

# VERIFICACIÓN DE INTEGRIDAD DE DATOS
if (verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}', $usuario_nombre)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            Su nombre no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if (verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}', $usuario_apellido)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            La apellido no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if (verificar_datos('[a-zA-Z0-9]{4,20}', $usuario_usuario)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            La usuario no coincide con el formato solicitado
        </div>
    ';
    exit();
};

// VALIDACIÓN EMAIL
// Si el email que ingresaron es diferente al que teníamos en nuestra DB...
if ($usuario_email != "" &&  $usuario_email != $datos['email']) {
    // Si el email no es válido salimos del programa
    if (!filter_var($usuario_email, FILTER_VALIDATE_EMAIL)) {
        echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                El email no coincide con el formato solicitado
            </div>
        ';
        exit();
    };
    // Si el email es válido verificaremos que no exista en nuestra tabla
    $pdo = conexion();
    $check_email = $pdo->query("SELECT email FROM usuarios WHERE email='$usuario_email'");
    // Si existe saldremos del programa
    if ($check_email->rowCount() >= 1) {
        echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                El email ya existe en nuestros registros, por favor, elija otro.
            </div>
        ';
        exit();
    }
    // Si no existe todo está correcto, el email es válido para actualizarlo y cerramos la conexión a nuestra DB
    $pdo = null;
}

// VALIDACIÓN USUARIO
// Si el usuario que ingresaron es diferente al que teníamos en nuestra DB...
if ($usuario_usuario != $datos['usuario']) {
    $pdo = conexion();
    $check_email = $pdo->query("SELECT usuario FROM usuarios WHERE usuario='$usuario_usuario'");
    // Si existe saldremos del programa
    if ($check_email->rowCount() >= 1) {
        echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                El usuario ya existe en nuestros registros, por favor, elija otro.
            </div>
        ';
        exit();
    }
    // Si no existe todo está correcto, el usuario es válido para actualizarlo y cerramos la conexión a nuestra DB
    $pdo = null;
}

// VALIDACIÓN CLAVES
// Si las claves no vienen vacias, entonces, validamos claves
if ($usuario_clave_1 != "" || $usuario_clave_2 != "") {
    // Verificamos el pattern de la clave
    // Si no coindice salimos del programa
    if (verificar_datos('[a-zA-Z0-9$@.-]{7,100}', $usuario_clave_1)) {
        echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                La clave no coincide con el formato solicitado
            </div>
        ';
        exit();
    } else {
        // Si la Regex es válida evaluamos que las claves sean iguals, si no lo son salimos del programa
        if ($usuario_clave_1 != $usuario_clave_2) {
            echo '
                <div class="notification is-danger">
                <button class="delete"></button>
                Las claves que has ingresado no coinciden
                </div>
            ';
            exit();
        } else {
            // Si son iguales generamos la nueva pass a insertar
            $usuario_clave = password_hash($usuario_clave_1, PASSWORD_BCRYPT, ["cost" => 10]);
        }
    };
} else {
    // Si las claves vienen vacias es porque no se modificaron y tomamos la misma de la DB
    $usuario_clave = $datos["password"];
}


// ACTUALIZACIÓN DE DATOS
$pdo = conexion();

$user_update = $pdo->prepare("UPDATE usuarios SET nombre=:nombre, apellido=:apellido, usuario=:usuario, password=:password, email=:email WHERE usuario_id =:usuario_id");

$user_update->bindParam(':usuario_id', $id);
$user_update->bindParam(':nombre', $usuario_nombre);
$user_update->bindParam(':apellido', $usuario_apellido);
$user_update->bindParam(':usuario', $usuario_usuario);
$user_update->bindParam(':password', $usuario_clave);
$user_update->bindParam(':email', $usuario_email);

if ($user_update->execute()) {
    echo '
        <div class="notification is-info">
        <button class="delete"></button>
        Usuario actualizado con éxito
        </div>
    ';
    exit();
} else {
    echo '
        <div class="notification is-danger">
        <button class="delete"></button>
        Ha ocurrido un error al actualizar el usuario
        </div>
    ';
    exit();
}
$pdo = null;
