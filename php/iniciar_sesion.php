<?php

$nombre = limpiar_cadena($_POST['login_usuario']);
$clave = limpiar_cadena($_POST['login_clave']);



// VERIFICACIÓN DE DATOS
# verificamos los campos obligatorios

if ($nombre == '' || $clave == '') {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Datos incorrectos
    </div>
    ';
    exit();
}
# VERIFICACIÓN DE INTEGRIDAD DE DATOS
if (verificar_datos('[a-zA-Z0-9]{4,20}', $nombre)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            El nombre no coincide con el formato solicitado
        </div>
    ';
    exit();
};

if (verificar_datos('[a-zA-Z0-9$@.-]{7,100}', $clave)) {
    echo '
        <div class="notification is-danger">
            <button class="delete"></button>
            La clave no coincide con el formato solicitado
        </div>
    ';
    exit();
};
# COMPROBANDO DATOS

// $pass_pass =  password_hash($clave, PASSWORD_BCRYPT, ["cost" => 10]);


$pdo = conexion();
$check_auth = $pdo->query("SELECT password FROM usuarios WHERE usuario = '$nombre'");
// print_r($check_auth->fetch());
if (password_verify($clave, $check_auth->fetch()[0])) {
    echo 'La contraseña es válida!';
} else {
    echo 'La contraseña no es válida.';
}
