<?php
// Recibimos el modulo que estamus buscando
$modulo_buscador = limpiar_cadena($_POST['modulo_buscador']);
// Definimos nuestros modulos de busqueda
$modulos = ["usuario", "categoria", "producto"];
// Si se encuentra dentro de nuestros modulos prosegimos, si no, enviamos el error
if (in_array($modulo_buscador, $modulos)) {
    $modulos_url = [
        "usuario" => "user_search",
        "categoria" => "category_search",
        "producto" => "product_search"
    ];
    // Traemos nuestra vista
    $modulos_url = $modulos_url[$modulo_buscador];
    // Traemos nuestra variable de session
    $modulo_buscador = "busqueda_" . $modulo_buscador;

    // Iniciar busqueda
    if (isset($_POST['txt_buscador'])) {
        $txt = limpiar_cadena($_POST['txt_buscador']);
        // Si la busqueda es vacia...
        if ($_POST['txt_buscador'] == "") {
            echo '
            <div class="notification is-danger">
                <button class="delete"></button>
                Introduce un termino de busqueda
            </div>
            ';
        } else {
            // Verificamos el pattern de nuestro input
            if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}", $txt)) {
                echo '
                <div class="notification is-danger">
                    <button class="delete"></button>
                    El termino de busqueda no coincede con el formato solicitado
                </div>
                ';
            } else {
                // A nuestra variable de session le asignamos el valor sanitizado
                $_SESSION[$modulo_buscador]  = $txt;
                // Recargamos la página
                // Hacemos esto por el condicional de nuestro archivo de busqueda, el cual entrará en el lado del if donde realizará la busqueda con nuestra valor de la variable de session
                // Con el parametro true y 303 le decimos al servidor que reenvie al usuario sin volver a mandar el forumulario
                header("Location: index.php?vista=$modulos_url", true, 303);
                exit();
            }
        }
    }

    // Eliminar busqueda
    if (isset($_POST['eliminar_buscador'])) {
        // Con unset eliminamos el valor de la variable de session
        unset($_SESSION[$modulo_buscador]);
        // Redirigimos al usuario
        header("Location: index.php?vista=$modulos_url", true, 303);
        exit();
    }
} else {
    echo '
    <div class="notification is-danger">
        <button class="delete"></button>
        No podemos procesar la petición
    </div>
    ';
}
