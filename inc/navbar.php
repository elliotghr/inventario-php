<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="index.php?vista=home">
            <!-- <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28"> -->
            <h2><b style="font-size: 1.5rem;">Abarrotes S.A de C.V.</b></h2>
        </a>

        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-start">
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Usuarios
                </a>

                <div class="navbar-dropdown">
                    <a class="navbar-item" href="index.php?vista=user_new">
                        Nuevo
                    </a>
                    <a class="navbar-item" href="index.php?vista=user_list">
                        Lista
                    </a>
                    <a class="navbar-item" href="index.php?vista=user_search">
                        Buscar
                    </a>
                </div>
            </div>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Categorias
                </a>

                <div class="navbar-dropdown">
                    <a href="index.php?vista=category_new" class="navbar-item">
                        Nuevo
                    </a>
                    <a href="index.php?vista=category_list" class="navbar-item">
                        Lista
                    </a>
                    <a href="index.php?vista=category_search" class="navbar-item">
                        Buscar
                    </a>
                </div>
            </div>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Productos
                </a>

                <div class="navbar-dropdown">
                    <a href="index.php?vista=product_new" class="navbar-item">
                        Nuevo
                    </a>
                    <a href="index.php?vista=product_list" class="navbar-item">
                        Lista
                    </a>
                    <a href="index.php?vista=product_category" class="navbar-item">
                        Por categorias
                    </a>
                    <a href="index.php?vista=product_search" class="navbar-item">
                        Buscar
                    </a>
                </div>
            </div>
        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <a href="index.php?vista=user_update&user_id_up=<?php echo $_SESSION['id']; ?>" class="button is-primary is-rounded">
                        Mi cuenta
                    </a>
                    <a href="index.php?vista=logout" class="button is-light is-rounded">
                        Salir
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>