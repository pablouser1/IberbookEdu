<?php
// Initialize the session
session_start();
require_once("../helpers/db.php");
require_once("../helpers/config.php");
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("location: ../login.php");
}

$userinfo = $_SESSION["userinfo"];

// Teachers
$stmt = $conn->prepare("SELECT id, fullname, picname, vidname, link, quote, DATE_FORMAT(uploaded, '%d/%m/%Y %H:%i'), subject FROM teachers where schoolid=? and schoolyear=?");
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

// Students
$stmt = $conn->prepare("SELECT id, fullname, picname, vidname, link, quote, DATE_FORMAT(uploaded, '%d/%m/%Y %H:%i') FROM students where schoolid=? and schoolyear=?");
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

// Gallery

$stmt = $conn->prepare("SELECT id, picname, picdescription FROM gallery where schoolid=? and schoolyear=?");
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

// Check if admin generated yearbook before
$stmt = $conn->prepare("SELECT DATE_FORMAT(generated, '%d/%m/%Y %H:%i'), available FROM yearbooks WHERE schoolid=? AND schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($generated, $available);
if ($stmt->num_rows == 1) {
    if(($result = $stmt->fetch()) == true){
        $yearbook = array(
            "date" => $generated,
            "available" => $available,
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
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
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
        <p class="subtitle">Total: <?php echo(count($teachers));?></p>
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
                            echo <<<EOL
                                <td>$teacher[0]</td>
                                <td>$teacher[1]</td>
                                <td><a href='../getmedia.php?id=$teacher[0]&media=picname&type=P' target='_blank'>$teacher[2]</a></td>
                                <td><a href='../getmedia.php?id=$teacher[0]&media=vidname&type=P' target='_blank'>$teacher[3]</a></td>
                            EOL;
                            if (empty($teacher[4])) echo '<td class="has-text-centered">-</td>';
                            else echo '<td><a href="'.$teacher[4].'" target="_blank">Abrir enlace</a></td>';
    
                            if (empty($teacher[5])) echo("<td class='has-text-centered'>-</td>");
                            else echo '<td>'.$teacher[5].'</td>';
    
                            echo <<<EOL
                            <td>$teacher[6]</td>
                            <td>$teacher[7]</td>
                            EOL;
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
        <p class="subtitle">Total: <?php echo(count($students));?></p>
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
                            echo <<<EOL
                            <tr>
                                <td>$student[0]</td>
                                <td>$student[1]</td>
                                <td><a href='../getmedia.php?id=$student[0]&media=picname&type=ALU' target='_blank'>$student[2]</a></td>
                                <td><a href='../getmedia.php?id=$student[0]&media=vidname&type=ALU' target='_blank'>$student[3]</a></td>
                            EOL;
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
        <hr>
        <p class="title">Administrar datos</p>
        <div class="field is-grouped">
            <div class="control">
                <div class="select">
                    <select id="select_user">
                    <?php
                    if (isset($teachers)){
                        echo("<option disabled>Profesores</option>");
                        foreach ($teachers as $teacher){
                            echo ('<option value="'.$teacher[0].'">'.$teacher[1].'</option>');
                        }
                    }
                    if (isset($students)){
                        echo("<option disabled>Alumnos</option>");
                        foreach ($students as $student){
                            echo ('<option value="'.$student[0].'">'.$student[1].'</option>');
                        }
                    }
                    ?>
                    </select>
                </div>
            </div>
            <div class="control">
                <button id="delete_user" class="button is-danger <?php if(!isset($teachers, $students)) echo('is-disabled');?>">
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
            <i class="far fa-images"></i>
            <span>Galería</span>
        </p>
        <p class="subtitle">Total: <?php echo(count($gallery));?></p>
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get all values from gallery's table
                    foreach($gallery as $picture){
                        echo <<<EOL
                        <tr>
                            <td>$picture[0]</td>
                            <td><a href='../getgallery.php?id=$picture[0]' target='_blank'>$picture[1]</a></td>
                            <td>$picture[2]</td>
                        </tr>
                        EOL;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        // -- Yearbook options when generated -- //
        if (isset($yearbook)){
            echo '
            <hr>
            <h1 class="title">Yearbook</h1>
            <p class="subtitle">Generado el '.$yearbook["date"].'';
            if($yearbook["available"] == 0){
                echo('<p class="subtitle">Sólo los <strong>administradores</strong> pueden ver el yearbook');
            }
            else{
                echo('<p class="subtitle"><strong>Los usuarios y administradores</strong> pueden ver el yearbook');
            }
            echo '
            <div class="buttons">
                <a href="../getyearbook.php" class="button is-primary">
                    <span class="icon">
                        <i class="fas fa-download"></i>
                    </span>
                    <span>Descargar yearbook</span>
                </a>
                <a href="manageyb.php?deleteyearbook=true" class="button is-danger">
                    <span class="icon">
                        <i class="fas fa-trash"></i>
                    </span>
                    <span>Eliminar yearbook</span>
                </a>
            </div>
            ';
            // Show if admin didn't make the yearbook available for regular users
            if($yearbook["available"] == 0){
                echo '
                <a href="manageyb.php?makeavailable=true" class="button is-link">
                    <span class="icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <span>Alternar permisos de visionado a usuarios</span>
                </a>
                ';
            }
        }
        ?>
    </section>
    <div id="progress" class="is-hidden container">
        <p class="subtitle">Generando yearbook, este proceso puede tardar varios minutos</p>
        <progress class="progress is-primary" max="100"></progress>
    </div>
    <section class="section <?php if(isset($yearbook)) echo("is-hidden");?>">
        <div class="buttons">
            <a id="genyearbook" class="button is-success" href="send.php">
                <span class="icon">
                    <i class="fas fa-check"></i>
                </span>
                <span>Generar Yearbook</span>
            </a>
            <a class="button is-info" href="gallery.php">
                <span class="icon">
                    <i class="far fa-images"></i>
                </span>
                <span>Agregar fotos a galería</span>
            </a>
        </div>
    </section>
    <footer class="footer">
        <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
            <ul>
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
