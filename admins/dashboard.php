<?php
// Initialize the session
session_start();
require_once("../helpers/db.php");
require_once("../helpers/config.php");
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("Location: ../login.php");
    exit;
}

$userinfo = $_SESSION["userinfo"];

// Teachers
$stmt = $conn->prepare("SELECT id, fullname, photo, video, link, quote, DATE_FORMAT(uploaded, '%d/%m/%Y %H:%i'), subject FROM teachers where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $teachers[$id] = array();
    foreach ($row as $field => $value) {
        $teachers[$id][] = $value;
    }
}
$stmt->close();
// Get amount teachers
$teachers_amount = (!isset($teachers)) ? 0 : count($teachers);

// Students
$stmt = $conn->prepare("SELECT id, fullname, photo, video, link, quote, DATE_FORMAT(uploaded, '%d/%m/%Y %H:%i') FROM students where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $students[$id] = array();
    foreach ($row as $field => $value) {
        $students[$id][] = $value;
    }
}
$stmt->close();
// Get amount students
$students_amount = (!isset($students)) ? 0 : count($students);

// Gallery

$stmt = $conn->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$gallery_i = 0;
while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $gallery[$id] = array();
    foreach ($row as $field => $value) {
        $gallery[$id][] = $value;
    }
    $gallery_i++;
}
$stmt->close();
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
                                <td>$teacher[0]</td>
                                <td>$teacher[1]</td>
                                <td><a href='../getmedia.php?id=$teacher[0]&media=photo&type=P' target='_blank'>$teacher[2]</a></td>
                                <td><a href='../getmedia.php?id=$teacher[0]&media=video&type=P' target='_blank'>$teacher[3]</a></td>
                            ";
                            if (empty($teacher[4])) echo '<td class="has-text-centered">-</td>';
                            else echo '<td><a href="'.$teacher[4].'" target="_blank">Abrir enlace</a></td>';
    
                            if (empty($teacher[5])) echo("<td class='has-text-centered'>-</td>");
                            else echo '<td>'.$teacher[5].'</td>';
    
                            echo "
                            <td>$teacher[6]</td>
                            <td>$teacher[7]</td>
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
                            <tr>
                                <td>$student[0]</td>
                                <td>$student[1]</td>
                                <td><a href='../getmedia.php?id=$student[0]&media=photo&type=ALU' target='_blank'>$student[2]</a></td>
                                <td><a href='../getmedia.php?id=$student[0]&media=video&type=ALU' target='_blank'>$student[3]</a></td>
                            ";
                            if (empty($student[4])) echo '<td class="has-text-centered">-</td>';
                            else echo '<td><a href="'.$student[4].'" target="_blank">Abrir enlace</a></td>';
    
                            if (empty($student[5])) echo('<td class="has-text-centered">-</td>');
                            else echo '<td>'.$student[5].'</td>';
                            echo '<td>'.$student[6].'</td></tr>';
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
                            echo ('<option value="'.$teacher[0].'">'.$teacher[1].'</option>');
                        }
                    }
                    else echo("<option>No hay profesores disponibles");
                    echo("<option disabled>Alumnos</option>");
                    if (isset($students)){
                        foreach ($students as $student){
                            echo ('<option value="'.$student[0].'">'.$student[1].'</option>');
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
                                <td>$item[0]</td>
                                <td><a href='../getgallery.php?id=$item[0]' target='_blank'>$item[1]</a></td>
                                <td>$item[2]</td>
                                <td>$item[3]</td>
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
        <div class="buttons">
            <button id="genyearbook" class="button is-success" <?php if(!isset($students, $teachers)) echo("disabled"); ?>>
                <span class="icon">
                    <i class="fas fa-check"></i>
                </span>
                <span>Generar Yearbook</span>
            </button>
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

