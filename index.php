<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IberbookEdu</title>
    <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css"/>
</head>

<body>
    <section class="hero is-primary is-fullheight">
        <!-- Head -->
        <div class="hero-head">
            <nav class="navbar is-transparent" role="navigation" aria-label="main navigation">
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
                        <?php
                        // If user is logged in
                        if (isset($_SESSION["loggedin"])) {
                            echo '
                            <a class="navbar-item" href="users/dashboard.php">
                                <span class="icon">
                                    <i class="fas fa-user"></i>
                                </span>
                                <span><b>Panel de control</b></span>
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
                                <span><b>Iniciar sesión</b></span>
                            </a>
                            ';
                        }
                        ?>
                        <a class="navbar-item" href="yearbooks.php">
                            <span class="icon">
                                <i class="fas fa-book"></i>
                            </span>
                            <span><b>Yearbooks</b></span>
                        </a>
                        <a class="navbar-item" href="about.html">
                            <span class="icon">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            <span><b>Acerca de</b></span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Body -->
        <div class="hero-body">
            <div class="container has-text-centered animate__animated animate__jackInTheBox">
                <h1 class="title">
                    Bienvenido a IberbookEdu
                </h1>
                <h2 class="subtitle">
                    IberbookEdu es un generador de orlas usando los datos de PASEN/SENECA o ROBLE
                </h2>
            </div>
        </div>
    </section>
    <script src="assets/scripts/index.js"></script>
</body>

</html>

