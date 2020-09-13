<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("Location: ../login.php");
    exit;
}

require_once("../helpers/db/db.php");
require_once("../helpers/config.php");
require_once("yearbook/themes.php");

$userinfo = $_SESSION["userinfo"];

require_once("../helpers/db/getinfo.php");

$DBInfo = new DBInfo($userinfo);
$teachers = $DBInfo->teachers();
// Get amount teachers
$teachers_amount = (!isset($teachers)) ? 0 : count($teachers);

$students = $DBInfo->students();
// Get amount students
$students_amount = (!isset($students)) ? 0 : count($students);

$gallery = $DBInfo->gallery();
// Get amount items
$gallery_amount = (!isset($gallery)) ? 0 : count($gallery);

// Check if admin generated yearbook before
$stmt = $conn->prepare("SELECT DATE_FORMAT(generated, '%d/%m/%Y %H:%i') FROM yearbooks WHERE schoolid=? AND schoolyear=?");
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
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard admins - IberbookEdu</title>
    <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
</head>
<body>
    <section class="hero is-primary is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    Bienvenido, <?php echo($userinfo["nameuser"]);?>
                </h1>
                <h2 class="subtitle">
                    Estás viendo la información del curso <?php echo($userinfo["yearuser"]);?> del centro <?php echo($userinfo["namecentro"]);?>
                </h2>
            </div>
        </div>
    </section>
    <section class="section">
        <!-- Teachers -->
        <p class="title">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Profesores</span>
        </p>
        <p class="subtitle">Total: <?php echo($teachers_amount); ?></p>
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre completo</th>
                        <th>Foto</th>
                        <th>Vídeo</th>
                        <th>Enlace</th>
                        <th>Cita</th>
                        <th>Fecha de subida</th>
                        <th>Asignatura</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php
                    // Get all values from teachers' table
                    if (!isset($teachers)) echo '<td>No hay profesores disponibles</td>';
                    else {
                        foreach($teachers as $teacher){
                            echo "
                                <td>$teacher[id]</td>
                                <td>$teacher[name]</td>
                                <td><a href='../getmedia.php?id=$teacher[id]&media=photo&type=P' target='_blank'>$teacher[photo]</a></td>
                                <td><a href='../getmedia.php?id=$teacher[id]&media=video&type=P' target='_blank'>$teacher[video]</a></td>
                                <td><a href='$teacher[link]' target='_blank'>Abrir enlace</a></td>
                                <td>$teacher[quote]</td>
                                <td>$teacher[uploaded]</td>
                                <td>$teacher[subject]</td>
                            ";
                        }
                    }
                    ?>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Students -->
        <p class="title">
            <i class="fas fa-user-graduate"></i>
            <span>Alumnos</span>
        </p>
        <p class="subtitle">Total: <?php echo($students_amount);?></p>
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre y apellidos</th>
                        <th>Foto</th>
                        <th>Vídeo</th>
                        <th>Enlace</th>
                        <th>Cita</th>
                        <th>Fecha de subida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get all values from students' table
                    if (!isset($students)) echo '<td>No hay alumnos disponibles</td>';
                    else {
                        foreach($students as $student){
                            echo "
                                <td>$student[id]</td>
                                <td>$student[name]</td>
                                <td><a href='../getmedia.php?id=$student[id]&media=photo&type=ALU' target='_blank'>$student[photo]</a></td>
                                <td><a href='../getmedia.php?id=$student[id]&media=video&type=ALU' target='_blank'>$student[video]</a></td>
                                <td><a href='$student[link]' target='_blank'>Abrir enlace</a></td>
                                <td>$student[quote]</td>
                                <td>$student[uploaded]</td>
                            ";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <p class="title">Administrar usuarios</p>
        <div class="field is-grouped">
            <div class="control">
                <div class="select">
                    <select id="select_user">
                    <?php
                    echo("<option disabled>Profesores</option>");
                    if (isset($teachers)){
                        foreach ($teachers as $teacher){
                            echo ("<option value='$teacher[id]'>$teacher[name]</option>");
                        }
                    }
                    else echo("<option>No hay profesores disponibles");
                    echo("<option disabled>Alumnos</option>");
                    if (isset($students)){
                        foreach ($students as $student){
                            echo ("<option value='$student[id]'>$student[name]</option>");
                        }
                    }
                    else echo("<option>No hay alumnos disponibles</option>");
                    ?>
                    </select>
                </div>
            </div>
            <div class="control">
                <button id="delete_user" class="button is-danger" <?php if(!isset($teachers) && !isset($students)) echo('disabled');?>>
                    <span class="icon">
                        <i class="fas fa-trash"></i>
                    </span>
                    <span>Eliminar datos</span>
                </button>
            </div>
        </div>
        <hr>
        <!-- Gallery -->
        <p class="title">
            <i class="fas fa-photo-video"></i>
            <span>Galería</span>
        </p>
        <p class="subtitle">Total: <?php echo($gallery_amount); ?></p>
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get all values from gallery's table
                    if (!isset($gallery)) echo '<td>No hay fotos disponibles</td>';
                    else {
                        foreach($gallery as $item){
                            echo "
                            <tr>
                                <td>$item[id]</td>
                                <td><a href='../getgallery.php?id=$item[id]' target='_blank'>$item[name]</a></td>
                                <td>$item[description]</td>
                                <td>$item[type]</td>
                            </tr>
                            ";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        // -- Yearbook options when generated -- //
        if (isset($yearbook)){
            $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
            $params = "?schoolid=$userinfo[idcentro]&acyear=$acyear&group=$userinfo[yearuser]";
            echo "
            <hr>
            <h1 class='title'>Yearbook</h1>
            <p class='subtitle'>Generado el $yearbook[date]
            <div class='buttons'>
                <a href='../yearbooks.php{$params}' target='_blank' class='button is-primary'>
                    <span class='icon'>
                        <i class='fas fa-eye'></i>
                    </span>
                    <span>Ver yearbook</span>
                </a>
                <a href='manageyb.php?action=delete' class='button is-danger'>
                    <span class='icon'>
                        <i class='fas fa-trash'></i>
                    </span>
                    <span>Eliminar yearbook</span>
                </a>
            </div>
            ";
        }
        ?>
    </section>
    <div id="progress" class="is-hidden container">
        <p class="subtitle">Generando yearbook, este proceso puede tardar varios minutos, por favor <strong>NO</strong> cierre su navegador</p>
        <progress class="progress is-primary" max="100"></progress>
    </div>
    <section class="section <?php if(isset($yearbook)) echo("is-hidden");?>">
        <p class="title">Administrar yearbook</p>
        <div class="field">
            <label class="label">Plantilla</label>
            <div class="control">
                <div class="select">
                    <select id="theme_selector">
                        <?php
                        foreach ($themes as $theme) {
                            echo("<option>$theme</option>");
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="buttons">
            <a id="genyearbook" href="yearbook/send.php?theme=default" class="button is-success">
                <span class="icon">
                    <i class="fas fa-check"></i>
                </span>
                <span>Generar Yearbook</span>
            </a>
            <a class="button is-info" href="gallery.php">
                <span class="icon">
                    <i class="fas fa-photo-video"></i>
                </span>
                <span>Modificar galería</span>
            </a>
        </div>
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
                <?php
                if (!isset($_SESSION["owner"])) {
                    echo '
                    <li>
                        <a href="../users/dashboard.php">
                            <span class="icon is-small">
                                <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                            </span>
                            <span>Cambiar a usuario</span>
                        </a>
                    </li>
                    ';
                }
                else {
                    echo '
                    <li>
                        <a href="../owner/dashboard.php">
                            <span class="icon is-small">
                                <i class="fas fa-exchange-alt" aria-hidden="true"></i>
                            </span>
                            <span>Volver al panel de control de dueño</span>
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
    <script src="../assets/scripts/admins/dashboard.js"></script>
</body>
</html>

