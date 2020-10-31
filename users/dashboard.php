<?php
session_start();
if(!isset($_SESSION["loggedin"])){
    header("Location: ../login.php");
    exit;
}

// User data
$userinfo = $_SESSION["userinfo"];

require_once("../helpers/getinfo.php");
$DBInfo = new DBInfo($userinfo);

$user_values = $DBInfo->user();

$gallery = $DBInfo->gallery();

$yearbook = $DBInfo->yearbook();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard usuarios - IberbookEdu</title>
    <link rel="stylesheet" href="../assets/styles/dashboard.css"/>
    <!-- Dev -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> -->
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
        // Initial variables
        const user_js = <?php echo(json_encode($user_values));?>;
        const gallery_js = <?php echo(json_encode($gallery));?>;
        const yearbook_js = <?php echo(json_encode($yearbook));?>;
    </script>
</head>

<body>
    <div id="main">
        <!-- Start navbar -->
        <nav class="navbar is-white">
            <div class="container">
                <div class="navbar-brand">
                    <a class="navbar-item brand-text" href="../index.php">
                        <span class="icon">
                            <i class="fas fa-home"></i>
                        </span>
                        <span><b>IberbookEdu</b></span>
                    </a>
                    <a class="navbar-burger" :class="{ 'is-active': showNav }" @click="showNav = !showNav" role="button" aria-label="menu" aria-expanded="false">
                        <span></span>
                        <span></span>
                        <span></span>
                    </a>
                </div>
                <div class="navbar-menu" :class="{ 'is-active': showNav }">
                    <div class="navbar-end">
                        <div class="navbar-item has-dropdown is-hoverable is-hidden-desktop">
                            <a class="navbar-link">
                                <span class="icon">
                                    <i class="fas fa-user"></i>
                                </span>
                                <span>Tabs</span>
                            </a>
                            <div class="navbar-dropdown">
                                <a v-on:click="changeTab('mainmenu')" class="navbar-item">Menú principal</a>
                                <a v-on:click="changeTab('user')" class="navbar-item">Mis datos</a>
                                <a v-on:click="changeTab('gallery')" class="navbar-item">Galería</a>
                            </div>
                        </div>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <span class="icon">
                                    <figure class="image">
                                        <img class="is-rounded" src="data:image/png;base64, <?php echo($userinfo["photouser"]);?>">
                                    </figure>
                                </span>
                                <span>Mi perfil</span>
                            </a>
                            <div class="navbar-dropdown">
                                <a class="navbar-item">
                                    <span class="icon">
                                        <i class="fas fa-cogs"></i>
                                    </span>
                                    <span>Ajustes</span>
                                </a>
                                <?php if($_SESSION["loggedin"] == "admin"):?>
                                    <a class="navbar-item" href="../admins/dashboard.php">
                                        <span class="icon">
                                            <i class="fas fa-exchange-alt"></i>
                                        </span>
                                        <span>Cambiar a administrador</span>
                                    </a>
                                <?php endif;?>
                                <?php if(isset($_SESSION["tutorinfo"])): ?>
                                    <a class="navbar-item" href="../profiles/tutorlegal.php">
                                        <span class="icon">
                                            <i class="fas fa-exchange-alt"></i>
                                        </span>
                                        <span>Cambiar hijo</span>
                                    </a> 
                                <?php endif;?>
                                <?php if(isset($_SESSION["teacherinfo"])):?>
                                    <a class="navbar-item" href="../profiles/teachers.php">
                                        <span class="icon">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        </span>
                                        <span>Cambiar centro escolar / grupo</span>
                                    </a>
                                <?php endif;?>
                                <hr class="navbar-divider">
                                <a class="navbar-item" href="../logout.php">
                                    <span class="icon">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </span>
                                    <span>Cerrar sesión</span>
                                </a>
                            </div>
                        </div>
                        <a class="navbar-item" href="../yearbooks.php">
                            <span class="icon">
                                <i class="fas fa-book"></i>
                            </span>
                            <span>Yearbooks</span>
                        </a>
                        <a class="navbar-item" href="../about.html">
                            <span class="icon">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            <span>Acerca de</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <!-- End navbar -->
        <div class="container">
            <section class="hero is-info welcome is-small">
                <div class="hero-body">
                    <div class="container">
                        <h1 class="title has-text-centered">
                            Bienvenido: <?php echo($userinfo["nameuser"]);?>
                        </h1>
                    </div>
                </div>
            </section>
            <div class="columns is-centered">
                <div class="column is-3">
                    <aside class="menu is-hidden-mobile">
                        <p class="menu-label">General</p>
                        <ul class="menu-list">
                            <li><a :class="{'is-active': tab === 'mainmenu'}" v-on:click="changeTab('mainmenu')">Menú principal</a></li>
                        </ul>
                        <p class="menu-label">Mis datos</p>
                        <ul class="menu-list">
                            <li><a :class="{'is-active': tab === 'user'}" v-on:click="changeTab('user')">Subida</a></li>
                        </ul>
                        <p class="menu-label">Mi grupo</p>
                        <ul class="menu-list">
                            <li><a :class="{'is-active': tab === 'gallery'}" v-on:click="changeTab('gallery')">Galería</a></li>
                        </ul>
                    </aside>
                </div>
                <div class="column is-9">
                    <section id="items" class="section">
                        <!-- Main Menu --->
                        <mainmenu class="animate__animated animate__fadeIn" v-if="tab === 'mainmenu'" v-bind:user="user" v-bind:yearbook="yearbook"></mainmenu>
                        <!-- User -->
                        <user class="animate__animated animate__fadeIn" v-if="tab === 'user'" v-bind:user="user"></user>
                        <!-- Gallery -->
                        <gallery class="animate__animated animate__fadeIn" v-if="tab === 'gallery'" v-bind:gallery="gallery"></gallery>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/scripts/users/dashboard.js"></script>
</body>

</html>
