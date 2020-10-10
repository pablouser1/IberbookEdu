<?php
session_start();
if(!isset($_SESSION["loggedin"])){
    header("Location: ../login.php");
    exit;
}

require_once("../helpers/db/db.php");

// User data
$userinfo = $_SESSION["userinfo"];
$db = new DB;
$acyear = date("Y",strtotime("-1 year"))."-".date("Y");

// Check if yearbook is available
$yearbook = [
    "available" => false,
    "date" => null
];


$stmt = $db->prepare("SELECT generated FROM yearbooks WHERE schoolid=? AND schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($generated);
if ($stmt->num_rows == 1) {
    if(($result = $stmt->fetch()) == true) {
        $yearbook["available"] = true;
        $yearbook["date"] = $generated;
    }
}
$stmt->close();
// Check if user uploaded pic and vid before
if (isset($userinfo["subject"])){
    $stmt = $db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded, subject, reason
    FROM teachers WHERE schoolid=? AND schoolyear=?");
}
else{
    $stmt = $db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded, reason
    FROM students WHERE schoolid=? AND schoolyear=?");
}
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
// Set array with user values
$user_values = [];
if ($result->num_rows == 1){
    $user_values["type"] = $userinfo["typeuser"];
    while ($row = mysqli_fetch_assoc($result)) {
        foreach ($row as $field => $value) {
            $user_values[$field] = $value;
        }
    }
}
$stmt->close();

// Get gallery items
$stmt = $db->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
// Set array with values
$gallery = [];
if ($result->num_rows > 0){
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row["id"];
        foreach ($row as $field => $value) {
            $gallery[$id][$field] = $value;
        }
    }
}
$stmt->close();

?>
<!DOCTYPE html>
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Usuarios - IberbookEdu</title>
        <!-- Dev -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> -->
        <script src="https://cdn.jsdelivr.net/npm/vue"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        <script>
            const user_js = <?php echo(json_encode($user_values));?>;
            const gallery_js = <?php echo(json_encode($gallery));?>;
            const yearbook_js = <?php echo(json_encode($yearbook));?>;
        </script>
    </head>

    <body>
        <section class="hero is-primary is-bold">
            <div class="hero-body has-text-centered">
                <figure class="image container is-64x64">
                    <img src="data:image/png;base64, <?php echo($userinfo["photouser"]);?>" alt="Foto Perfil">
                </figure>
                <div class="container">
                    <h1 class="title"><?php echo($userinfo["nameuser"]);?></h1>
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
            <yearbook v-if="yearbook.available" v-bind:yearbook="yearbook"></yearbook>
            <dashboard v-if="Object.keys(user).length" v-bind:user="user"></dashboard>
            <upload v-else></upload>
            <gallery v-if="Object.keys(gallery).length" v-bind:gallery="gallery"></gallery>
        </section>
        <footer class="footer">
            <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
                <ul>
                    <?php if($_SESSION["loggedin"] == "admin"):?>
                        <!-- User is admin -->
                        <li>
                            <a href="../admins/dashboard.php">
                                <span class="icon is-small">
                                    <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                                </span>
                                <span>Cambiar a administrador</span>
                            </a>
                        </li>
                    <?php endif;?>

                    <?php if(isset($_SESSION["tutorinfo"])): ?>
                        <!-- User is parent -->
                        <li>
                            <a href="../profiles/tutorlegal.php">
                                <span class="icon is-small">
                                    <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                                </span>
                                <span>Cambiar hijo</span>
                            </a>
                        </li>
                    <?php endif;?>

                    <?php if(isset($_SESSION["teacherinfo"])):?>
                        <!-- User is teacher -->
                        <li>
                            <a href="../profiles/teachers.php">
                                <span class="icon is-small">
                                    <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
                                </span>
                                <span>Cambiar de curso/centro escolar</span>
                            </a>
                        </li>
                    }
                    <?php endif;?>
                    <!-- Cerrar sesión -->
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
        <script src="../assets/scripts/users/dashboard.js"></script>
    </body>
</html>
