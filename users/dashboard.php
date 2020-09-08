<?php
session_start();
if(!isset($_SESSION["loggedin"], $_SESSION["userinfo"])){
    header("Location: ../login.php");
    exit;
}
require_once("../helpers/db.php");

// User data
$userinfo = $_SESSION["userinfo"];
$typeuser = $_SESSION["typeuser"];
// Check if yearbook is available
$stmt = $conn->prepare("SELECT generated FROM yearbooks WHERE schoolid=? AND schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($generated);
if ($stmt->num_rows == 1) {
    if(($result = $stmt->fetch()) == true){
        $yearbook = array(
            "date" => $generated
        );
    }
}
$stmt->close();
// Check if user uploaded pic and vid before
if (isset($userinfo["subject"])){
    $stmt = $conn->prepare("SELECT id, fullname, photo, video, link, quote, DATE_FORMAT(uploaded, '%d/%m/%Y %H:%i'), subject
    FROM $typeuser where schoolid=? and schoolyear=?");
}
else{
    $stmt = $conn->prepare("SELECT id, fullname, photo, video, link, quote, DATE_FORMAT(uploaded, '%d/%m/%Y %H:%i')
    FROM $typeuser where schoolid=? and schoolyear=?");
}
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
// Set array with values
$user_values = [];
if ($result->num_rows > 0){
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row["id"];
        $user_values[$id] = array();
        foreach ($row as $field => $value) {
            $user_values[$id][] = $value;
        }
    }
}
$stmt->close();

// Get gallery items
$stmt = $conn->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
// Set array with values
$gallery = [];
if ($result->num_rows > 0){
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row["id"];
        $gallery[$id] = array();
        foreach ($row as $field => $value) {
            $gallery[$id][] = $value;
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
        <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
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
        <section id="dashboard" class="section">
            <?php
            if(isset($yearbook)){
                $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
                $params = "?schoolid=$userinfo[idcentro]&acyear=$acyear&group=$userinfo[yearuser]";
                echo "
                <section class='hero is-medium is-success is-bold'>
                    <div class='hero-body'>
                        <div class='container'>
                            <h1 class='title'>Tu yearbook está listo</h1>
                            <p class='subtitle'>
                                <a href='../yearbooks.php{$params}' target='_blank' class='button is-success'>
                                    <span class='icon'>
                                        <i class='fas fa-eye'></i>
                                    </span>
                                    <span>Ver</span>
                                </a>
                            </p>
                        </div>
                    </div>
                </section>
                <hr>
                ";
            }
            if (empty($user_values)){
                echo '
                <div class="content has-text-centered">
                    <h1 class="title">👋 ¡Hola! Bienvenido</h1>
                    <p>Parece ser que no tienes datos subidos, puedes comenzar pulsando el botón:</p>
                    <a class="button is-info" href="upload.php">
                        <span class="icon">
                            <i class="fas fa-upload"></i>
                        </span>
                        <span>Agregar datos</span>
                    </a>
                </div>
                ';
            }
            else {
                echo '
                <h1 class="title">
                    <i class="fas fa-upload"></i>
                    <span>Tus datos</span>
                </h1>
                ';
            }
            ?>
            <!-- Datos subidos del usuario -->
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable">
                    <thead>
                        <tr>
                            <?php
                            if(!empty($user_values)){
                                echo "
                                <th>ID</th>
                                <th>Nombre completo</th>
                                <th>Foto</th>
                                <th>Vídeo</th>
                                <th>Enlace</th>
                                <th>Cita</th>
                                <th>Fecha de subida</th>
                                ";
                                if($userinfo["typeuser"] == "P") echo("<th>Asignatura</th>");
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get all values from user' table
                        if (!empty($user_values)) {
                            foreach($user_values as $individual){
                                echo "
                                <tr>
                                    <td>$individual[0]</td>
                                    <td>$individual[1]</td>
                                    <td><a href='../getmedia.php?id=$individual[0]&media=photo&type=$userinfo[typeuser]' target='_blank'>$individual[2]</a></td>
                                    <td><a href='../getmedia.php?id=$individual[0]&media=video&type=$userinfo[typeuser]' target='_blank'>$individual[3]</a></td>
                                ";
                                if (empty($individual[4])) echo '<td class="has-text-centered">-</td>';
                                else echo '<td><a href="'.$individual[4].'" target="_blank">Abrir enlace</a></td>';

                                if (empty($individual[5])) echo '<td class="has-text-centered">-</td>';
                                else echo '<td>'.$individual[5].'</td>';

                                echo '<td>'.$individual[6].'</td>';
                                if($userinfo["typeuser"] == "P"){
                                    echo("<td>$individual[7]</td>");
                                }
                                echo("</tr>");
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Galería -->
            <p class='title'>
                <i class="fas fa-photo-video"></i>
                <span>Galería de tu grupo</span>
            </p>
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable">
                    <thead>
                        <tr>
                            <?php
                            if(!empty($gallery)){
                                echo "
                                <th>Archivo</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                ";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get all values from user' table
                        if (!empty($gallery)) {
                            foreach($gallery as $item){
                                echo "
                                <tr>
                                    <td><a href='../getgallery.php?id=$item[0]' target='_blank'>$item[1]</a></td>
                                    <td>$item[2]</td>
                                    <td>$item[3]</td>
                                </tr>
                                ";
                            }
                        }
                        else {
                            echo "
                            <tr>
                                <td>No hay ninguna foto disponible</td>
                            </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
            // Comprobar si ya hay información subida
            if (!empty($user_values) && !isset($yearbook)){
                echo '
                <hr>
                <button id="delete_button" class="button is-danger" type="button">
                    <span class="icon">
                        <i class="fas fa-trash"></i>
                    </span>
                    <span>Eliminar datos</span>
                </button>
                ';
            }
            ?>
        </section>
        <div id="delete_modal" class="modal">
            <div onclick="closedelete()" class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">¿Seguro?</p>
                </header>
                <section class="modal-card-body">
                    <p>Al eliminar los datos tendrás que <b>volver a subir tus datos otra vez</b></p>
                </section>
                <footer class="modal-card-foot">
                    <a href="managedata.php?action=delete" class="button">Continuar</a>
                    <button id="delete_cancel" type="button" class="button">Cancelar</button>
                </footer>
            </div>
        </div>
        <script src="../assets/scripts/users/dashboard.js"></script>
        <footer class="footer">
        <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
            <ul>
            <?php
            if($_SESSION["loggedin"] == "admin") {
                echo '
                <li>
                    <a href="../admins/dashboard.php">
                        <span class="icon is-small">
                            <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                        </span>
                        <span>Cambiar a administrador</span>
                    </a>
                </li>
                ';
            }
            if(isset($_SESSION["tutorinfo"])) {
                echo '
                <li>
                    <a href="../profiles/tutorlegal.php">
                        <span class="icon is-small">
                            <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                        </span>
                        <span>Cambiar hijo</span>
                    </a>
                </li>
                ';
            }
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
    </body>
</html>

