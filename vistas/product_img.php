<div class="container is-fluid mb-6">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle">Actualizar imagen de producto</h2>
</div>

<div class="container pb-6 pt-6">
    <?php
    // Importamos el btn
    include "./inc/btn_back.php";

    // Importamos funciones
    require_once "./php/main.php";

    // Definimos el valor del parametro product_id_up
    $id = (isset($_GET['product_id_up'])) ? $_GET['product_id_up'] : 0;

    /*== Verificando producto ==*/
    $pdo = conexion();
    $check_producto = $pdo->query("SELECT * FROM productos WHERE producto_id='$id'");

    // Si obtenemos la imagen...
    if ($check_producto->rowCount() > 0) {
        $datos = $check_producto->fetch(PDO::FETCH_ASSOC);
    ?>

        <div class="form-rest mb-6 mt-6"></div>

        <div class="columns">
            <div class="column is-two-fifths">
                <!-- Si existe la foto es diferente de "", renderizamos la foto -->
                <!-- Esto generará un form para poder eliminar la imagen -->
                <?php if (is_file("./img/productos/" . $datos['foto'])) { ?>
                    <figure class="image mb-6">
                        <img src="./img/productos/<?php echo $datos['foto']; ?>">
                    </figure>
                    <form class="FormularioAjax" action="./php/producto_img_eliminar.php" method="POST" autocomplete="off">
                        <input type="hidden" name="img_del_id" value="<?php echo $datos['producto_id']; ?>">
                        <p class="has-text-centered">
                            <button type="submit" class="button is-danger is-rounded">Eliminar imagen</button>
                        </p>
                    </form>
                <?php } else { ?>
                    <!-- Si la imagen es "", solo renderizará la imagen x defecto -->
                    <figure class="image mb-6">
                        <img src="./img/producto.png">
                    </figure>
                <?php } ?>
            </div>
            <div class="column">
                <!-- Siempre se generará un formulario para actualizar la imagen -->
                <form class="mb-6 has-text-centered FormularioAjax" action="./php/producto_img_actualizar.php" method="POST" enctype="multipart/form-data" autocomplete="off">

                    <h4 class="title is-4 mb-6"><?php echo $datos['nombre']; ?></h4>

                    <label>Foto o imagen del producto</label><br>
                    <!-- Id del producto oculto -->
                    <input type="hidden" name="img_up_id" value="<?php echo $datos['producto_id']; ?>">

                    <div class="file has-name is-horizontal is-justify-content-center mb-6">
                        <label class="file-label">
                            <input class="file-input" type="file" name="foto" accept=".jpg, .png, .jpeg">
                            <span class="file-cta">
                                <span class="file-label">Imagen</span>
                            </span>
                            <span class="file-name">JPG, JPEG, PNG. (MAX 3MB)</span>
                        </label>
                    </div>
                    <p class="has-text-centered">
                        <button type="submit" class="button is-success is-rounded">Actualizar</button>
                    </p>
                </form>
            </div>
        </div>
    <?php
    } else {
        include "./inc/error_alert.php";
    }
    $pdo = null;
    ?>
</div>