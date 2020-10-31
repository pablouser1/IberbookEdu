<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IberbookEdu</title>
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style type="text/css">
        img {
            padding: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>
    <section class="hero is-fullheight is-default is-bold">
        <!-- Head -->
        <div class="hero-head">
            <nav class="navbar" role="navigation" aria-label="main navigation">
                <div class="navbar-brand">
                    <a class="navbar-item">
                        <b>IberbookEdu</b>
                    </a>

                    <a role="button" id="navbar-burger" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarMain">
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                    </a>
                </div>

                <div id="navbarMain" class="navbar-menu">
                    <div class="navbar-end">
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <span class="icon">
                                    <i class="fas fa-user"></i>
                                </span>
                                <span>Mi perfil</span>
                            </a>
                            <div class="navbar-dropdown">
                                <?php
                                // If user is logged in
                                if (isset($_SESSION["loggedin"])) {
                                    echo '
                                    <a class="navbar-item" href="users/dashboard.php">
                                        <span class="icon">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <span>Panel de control</span>
                                    </a>
                                    ';
                                }
                                // User is not logged in
                                else {
                                    echo '
                                    <a class="navbar-item" href="login.php">
                                        <span class="icon">
                                            <i class="fas fa-user-circle"></i>
                                        </span>
                                        <span>Iniciar sesión</span>
                                    </a>
                                    ';
                                }
                                ?>
                                <?php if(isset($_SESSION["tutorinfo"])): ?>
                                    <a class="navbar-item" href="profiles/tutorlegal.php">
                                        <span class="icon">
                                            <i class="fas fa-exchange-alt"></i>
                                        </span>
                                        <span>Cambiar hijo</span>
                                    </a> 
                                <?php endif;?>
                                <?php if(isset($_SESSION["teacherinfo"])):?>
                                    <a class="navbar-item" href="profiles/teachers.php">
                                        <span class="icon">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        </span>
                                        <span>Cambiar centro escolar / grupo</span>
                                    </a>
                                <?php endif;?>
                                <hr class="navbar-divider">
                                <a class="navbar-item" href="logout.php">
                                    <span class="icon">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </span>
                                    <span>Cerrar sesión</span>
                                </a>
                            </div>
                        </div>
                        <a class="navbar-item" href="yearbooks.php">
                            <span class="icon">
                                <i class="fas fa-book"></i>
                            </span>
                            <span>Yearbooks</span>
                        </a>
                        <a class="navbar-item" href="about.html">
                            <span class="icon">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            <span>Acerca de</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Body -->
        <div class="hero-body">
            <div class="container has-text-centered">
                <div class="columns is-vcentered">
                    <div class="column is-5">
                        <figure class="image is-16by9">
                            <img id="banner_image">
                        </figure>
                        <figcaption id="banner_caption"></figcaption>
                    </div>
                    <div class="column is-6 is-offset-1">
                        <h1 class="title is-2">
                            Bienvenido a IberbookEdu
                        </h1>
                        <h2 class="subtitle is-4">
                            Genera tus orlas fácilmente
                        </h2>
                        <br>
                        <p class="has-text-centered">
                            <a href="about.html" class="button is-medium is-info is-outlined">
                                <span class="icon">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                                <span>Más información</span>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="assets/scripts/index.js"></script>
</body>

</html>

