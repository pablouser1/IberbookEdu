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
$teachers = $DBInfo->teachers();

// Get students
$students = $DBInfo->students();

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
    <!-- Dev -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
    const teachers_js = <?php echo(json_encode($teachers)); ?>;
    const students_js = <?php echo(json_encode($students)); ?>;
    const gallery_js = <?php echo(json_encode($gallery)); ?>;
    const yearbook_js = <?php echo(json_encode($yearbook)); ?>;
    </script>
</head>
<body>
    <section class="hero is-primary is-bold">
        <div class="hero-body has-text-centered">
            <figure class="image container is-64x64">
                <img src="data:image/png;base64, <?php echo($userinfo["photouser"]);?>" alt="Foto Perfil">
            </figure>
            <div class="container">
                <h1 class="title"><?php echo($userinfo["nameuser"]);?> / Administrador</h1>
                <h2 class="subtitle"><?php echo($userinfo["yearuser"]);?></h2>
            </div>
        </div>
    </section>
    <div class="tabs is-centered">
        <ul>
            <li>
                <a href="../index.php">
                    <span class="icon is-small">
                        <i class="fas fa-home" aria-hidden="true"></i>
                    </span>
                    <span>Inicio</span>
                </a>
            </li>
        </ul>
    </div>
    <section id="main" class="section">
        <noscript>Esta página necesita Javascript para funcionar</noscript>
        <!-- Teachers -->
        <teachers v-bind:teachers="teachers"></teachers>
        <!-- Students -->
        <students v-bind:students="students"></students>
        <!-- Gallery -->
        <gallery v-bind:gallery="gallery"></gallery>
        <!-- Yearbook -->
        <yearbook v-bind:yearbook="yearbook"></yearbook>
    </section>
    <footer class="footer">
        <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
            <ul>
                <?php
                if(isset($_SESSION["teacherinfo"])) {
                    echo '
                    <li>
                        <a href="../profiles/teachers.php">
                            <span class="icon is-small">
                                <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
                            </span>
                            <span>Cambiar de curso/centro escolar</span>
                        </a>
                    </li>
                    ';
                }
                ?>
                <li>
                    <a href="../users/dashboard.php">
                        <span class="icon is-small">
                            <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                        </span>
                        <span>Cambiar a usuario</span>
                     </a>
                </li>
                <li>
                    <a href="../logout.php">
                        <span class="icon is-small">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        </span>
                        <span>Cerrar sesión</span>
                    </a>
                </li>
            </ul>
        </nav>
    </footer>
    <script src="../assets/scripts/admins/dashboard.js"></script>
</body>
</html>
