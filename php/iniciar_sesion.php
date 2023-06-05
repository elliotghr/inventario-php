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

$pdo = conexion();
// Generamos la consulta preparada
$check_auth = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :nombre");
$check_auth->bindParam(':nombre', $nombre);
$check_auth->execute();

// Guardamos los resultados del fetch
$get_data = $check_auth->fetch(PDO::FETCH_ASSOC);

// En caso de que no devuelva nada retornamos el mensaje
if (!$get_data) {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        Usuario no encontrado.
    </div>
    ';
    return;
}
// Almacenamos los datos de validación y sesión
$get_pass = $get_data['password'];
$get_id = $get_data['usuario_id'];
$get_nombre = $get_data['nombre'];
$get_apellido = $get_data['apellido'];
$get_usuario = $get_data['usuario'];

// Comprobamos si está correcta o no la pass
if (password_verify($clave, $get_pass)) {
    // generamos los datos necesarios para nuestra sesión
    $_SESSION['id'] = $get_id;
    $_SESSION['nombre'] = $get_nombre;
    $_SESSION['apellido'] = $get_apellido;
    $_SESSION['usuario'] = $get_usuario;

    // Si se mandaron cabeceras js redireccionaremos con JS
    if (headers_sent()) {
        echo "
        <script>
            location.href='index.php?vista=home';
        </script>
        ";
    } else {
        // si no, generamos el redirect con PHP
        header("Location: index.php?vista=home");
    }
} else {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        La contraseña no es válida.
    </div>
    ';
}
