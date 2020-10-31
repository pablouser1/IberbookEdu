<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

$ownerinfo = $_SESSION["ownerinfo"];
require_once("getprivinfo.php");

$info = new DBPrivInfo;
$staff = $info->staff();
$schools = $info->schools();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard dueño - IberbookEdu</title>
    <link rel="stylesheet" href="../assets/styles/dashboard.css"/>
    <!-- Dev -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> -->
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
        // Initial vars
        const staff_js = <?php echo(json_encode($staff));?>;
        const schools_js = <?php echo(json_encode($schools));?>;
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
                                <a v-on:click="changeTab('users')" class="navbar-item">Subidas</a>
                            </div>
                        </div>
                        <a class="navbar-item" href="../logout.php">
                            <span class="icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </span>
                            <span>Cerrar sesión</span>
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
                            Bienvenido: <?php echo($ownerinfo["username"]);?>
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
                        <p class="menu-label">Información</p>
                        <ul class="menu-list">
                            <li><a :class="{'is-active': tab === 'users'}" v-on:click="changeTab('users')">Subidas</a></li>
                        </ul>
                    </aside>
                </div>
                <div class="column is-9">
                    <section id="items" class="section">
                        <!-- Main Menu --->
                        <mainmenu class="animate__animated animate__fadeIn" v-if="tab === 'mainmenu'" v-bind:staff="staff" v-bind:schools="schools"></mainmenu>
                        <!-- User -->
                        <users class="animate__animated animate__fadeIn" v-if="tab === 'users'"></users>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/scripts/owner/dashboard.js"></script>
</body>

</html>
