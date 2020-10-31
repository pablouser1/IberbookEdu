<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("Location: ../login.php");
    exit;
}

$userinfo = $_SESSION["userinfo"];

require_once("../helpers/getinfo.php");
require_once("yearbook/themes.php");

$DBInfo = new DBInfo($userinfo);
// Get teachers
$teachers = $DBInfo->users('teachers');

// Get students
$students = $DBInfo->users('students');

// Get recent activity
$recent = $DBInfo->recents();

// Get gallery
$gallery = $DBInfo->gallery();

// Get yearbook info
$yearbook = $DBInfo->yearbook();
$yearbook["themes"] = $themes;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard admins - IberbookEdu</title>
    <link rel="stylesheet" href="../assets/styles/dashboard.css"/>
    <!-- Dev -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
        // Initial variables
        const recent_js = <?php echo(json_encode($recent)); ?>;
        const teachers_js = <?php echo(json_encode($teachers)); ?>;
        const students_js = <?php echo(json_encode($students)); ?>;
        const gallery_js = <?php echo(json_encode($gallery)); ?>;
        const yearbook_js = <?php echo(json_encode($yearbook)); ?>;
    </script>
</head>

<body>
    <div id="main">
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
                                <a v-on:click="changeTab('users')" class="navbar-item">Usuarios</a>
                                <a v-on:click="changeTab('gallery')" class="navbar-item">Galería</a>
                                <a v-on:click="changeTab('yearbook')" class="navbar-item">Orla</a>
                            </div>
                        </div>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <span class="icon">
                                    <i class="fas fa-user"></i>
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
                                <a class="navbar-item" href="../users/dashboard.php">
                                    <span class="icon">
                                        <i class="fas fa-exchange-alt"></i>
                                    </span>
                                    <span>Cambiar a usuario</span>
                                </a>
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
        <div class="container">
            <section class="hero is-info welcome is-small">
                <div class="hero-body">
                    <div class="container">
                        <figure class="container image is-64x64">
                            <img src="data:image/png;base64, <?php echo($userinfo["photouser"]);?>" alt="Foto Perfil">
                        </figure>
                        <h1 class="title has-text-centered">
                            Bienvenido: <?php echo($userinfo["nameuser"]);?>
                        </h1>
                    </div>
                </div>
            </section>
            <div class="columns is-centered">
                <div class="column is-3-desktop is-hidden-mobile">
                    <aside class="menu">
                        <p class="menu-label">General</p>
                        <ul class="menu-list">
                            <li><a :class="{'is-active': tab === 'mainmenu'}" v-on:click="changeTab('mainmenu')">Menú principal</a></li>
                        </ul>
                        <p class="menu-label">Moderación</p>
                        <ul class="menu-list">
                            <li><a :class="{'is-active': tab === 'users'}" v-on:click="changeTab('users')">Usuarios</a></li>
                        </ul>
                        <p class="menu-label">Administración</p>
                        <ul class="menu-list">
                            <li><a :class="{'is-active': tab === 'gallery'}" v-on:click="changeTab('gallery')">Galería</a></li>
                            <li><a :class="{'is-active': tab === 'yearbook'}" v-on:click="changeTab('yearbook')">Orla</a></li>
                        </ul>
                    </aside>
                </div>
                <div class="column is-12-mobile">
                    <section id="items" class="section">
                        <mainmenu class="animate__animated animate__fadeIn" v-if="tab == 'mainmenu'" v-bind:info="info"></mainmenu>
                        <!-- Users -->
                        <div v-if="tab == 'users'">
                            <!-- Teachers -->
                            <users class="animate__animated animate__fadeIn" v-bind:users="teachers" v-bind:type="'teachers'"></users>
                            <!-- Students -->
                            <users class="animate__animated animate__fadeIn" v-bind:users="students" v-bind:type="'students'"></users>
                        </div>
                        <!-- Gallery -->
                        <gallery class="animate__animated animate__fadeIn" v-if="tab == 'gallery'" v-bind:gallery="gallery"></gallery>
                        <!-- Yearbook -->
                        <yearbook class="animate__animated animate__fadeIn" v-if="tab == 'yearbook'" v-bind:yearbook="yearbook"></yearbook>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/scripts/admins/dashboard.js"></script>
</body>

</html>
