<?php
// obtenemos las funciones del main
require_once "main.php";

// RECEPCIÓN DE DATOS
# recibimos los datos del fetch
$usuario_nombre = limpiar_cadena($_POST['usuario_nombre']);
$usuario_apellido = limpiar_cadena($_POST['usuario_apellido']);
$usuario_usuario = limpiar_cadena($_POST['usuario_usuario']);
$usuario_email = limpiar_cadena($_POST['usuario_email']);
$usuario_clave_1 = limpiar_cadena($_POST['usuario_clave_1']);
$usuario_clave_2 = limpiar_cadena($_POST['usuario_clave_2']);

// VERIFICACIÓN DE DATOS
# verificamos los campos obligatorios
// Preferible con JS
if ($usuario_nombre == '' || $usuario_apellido == '' || $usuario_clave_1 == '' || $usuario_clave_2 == '') {
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
if (verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}', $usuario_nombre)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El nombre no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if (verificar_datos('[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}', $usuario_apellido)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El apellido no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if (verificar_datos('[a-zA-Z0-9]{4,20}', $usuario_usuario)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El usuario no coincide con el formato solicitado
        </div>
    ';
    exit();
};

if (verificar_datos('[a-zA-Z0-9$@.-]{7,100}', $usuario_clave_1)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            La clave no coincide con el formato solicitado
        </div>
    ';
    exit();
};
if (verificar_datos('[a-zA-Z0-9$@.-]{7,100}', $usuario_clave_2)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            La clave no coincide con el formato solicitado
        </div>
    ';
    exit();
};

# verificamos que el email sea valido
// Preferible con JS
if ($usuario_email == '') {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            Introduce un email
        </div>
    ';
    exit();
} else {
    if (!filter_var($usuario_email, FILTER_VALIDATE_EMAIL)) {
        echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El email no coincide con el formato solicitado
        </div>
    ';
        exit();
    };
}

// Si es valido comprobamos que no exista en nuestra DB
// Creamos la conexión
$pdo = conexion();
// Lanzamos la consulta
$check_email = $pdo->query("SELECT email FROM usuarios WHERE email = '$usuario_email'");
// Contamos el número de registros
if ($check_email->rowCount()) {
    echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                El email ya existe
            </div>
        ';
    // Cerramos la conexión
    $pdo = null;
    exit();
}

//  Verificamos usuario
$check_user = $pdo->query("SELECT * FROM usuarios WHERE usuario = '$usuario_usuario'");
if ($check_user->rowCount()) {
    echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                El usuario ya existe
            </div>
        ';
    // Cerramos la conexión
    $pdo = null;
    exit();
}

// Verificando claves

if ($usuario_clave_1 != $usuario_clave_2) {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Las claves no coinciden
    </div>
';
    // Cerramos la conexión
    $pdo = null;
    exit();
} else {
    $clave_hash = password_hash($usuario_clave_1, PASSWORD_BCRYPT, ["cost" => 10]);
}

# INSERTANDO DATOS

// La manera más sencilla pero con problemas de seguridad a inyección SQL
// $insert = $pdo->query("INSERT INTO usuarios (usuario_id,nombre,apellido,usuario,email,password) VALUES (0,'$usuario_nombre','$usuario_apellido','$usuario_usuario','$usuario_email','$usuario_clave_1','$usuario_clave_2')");

// Preparamos una consulta por seguridad con prepare, esto se logra creando marcadores (:name)
$user_insert = $pdo->prepare("INSERT INTO usuarios (nombre,apellido,usuario,email,password) VALUES (:usuario_nombre,:usuario_apellido,:usuario_usuario,:usuario_email,:usuario_clave_1)");

// Creamos un array para nuestro query 
$marcadores = [
    ":usuario_nombre" => $usuario_nombre,
    ":usuario_apellido" => $usuario_apellido,
    ":usuario_usuario" => $usuario_usuario,
    ":usuario_email" => $usuario_email,
    ":usuario_clave_1" => $clave_hash,
];

// ejecutamos la consulta
$user_insert->execute($marcadores);

// Si se insertó nuestro registro...
if ($user_insert->rowCount() == 1) {
    echo '
    <div class="notification is-info">
        <button class="delete"></button>
        ¡Usuario registrado!
    </div>
';
} else {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Error al insertar el registro, intente de nuevo
    </div>
';
    // Cerramos la conexión
    $pdo = null;
    exit();
}

// Cerramos la conexión
$pdo = null;
