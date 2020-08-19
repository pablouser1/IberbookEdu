<?php
// Initialize the session
session_start();
require_once("../helpers/db.php");
require_once("../helpers/config.php");
// Check if the user is logged in, if not then redirect him to login page
if($_SESSION["loggedin"] !== "admin"){
    header("location: ../login.php");
    exit;
}

$userinfo = $_SESSION["userinfo"];

// Teachers
$stmt = $conn->prepare("SELECT id, fullname, picname, vidname, link, quote, DATE_FORMAT(uploaded, '%d/%m/%Y %H:%i'), subject FROM teachers where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$teachers_values = array();

while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $teachers_values[$id] = array();
    foreach ($row as $field => $value) {
        $teachers_values[$id][] = $value;
    }
}
$stmt->close();

// Students
$stmt = $conn->prepare("SELECT id, fullname, picname, vidname, link, quote, DATE_FORMAT(uploaded, '%d/%m/%Y %H:%i') FROM students where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$students_values = array();

while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $students_values[$id] = array();
    foreach ($row as $field => $value) {
        $students_values[$id][] = $value;
    }
}
$stmt->close();

// Gallery

$stmt = $conn->prepare("SELECT id, picname, picdescription FROM gallery where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$gallery_values = array();
$gallery_i = 0;
while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $gallery_values[$id] = array();
    foreach ($row as $field => $value) {
        $gallery_values[$id][] = $value;
    }
    $gallery_i++;
}
$stmt->close();

if (isset($_GET["makeavailable"]) && $_GET["makeavailable"] == "true"){
    // Make yearbook available to users
    $stmt = $conn->prepare("UPDATE yearbooks SET available=1 WHERE schoolid=? and schoolyear=?");
    $stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
    if ($stmt->execute() !== true) {
        die("Error updating record: " . $conn->error);
    }
    $stmt->close();
}

if (isset($_GET["deleteyearbook"]) && $_GET["deleteyearbook"] == "true"){
    // Delete yearbook
    function delete_files($target) {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
    
            foreach($files as $file){
                delete_files($file);
            }
    
            rmdir($target);
        } elseif(is_file($target)) {
            unlink($target);  
        }
    }
    $stmt = $conn->prepare("DELETE FROM yearbooks WHERE schoolid=? and schoolyear=?");
    $stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
    if ($stmt->execute() !== true) {
        die("Error updating record: " . $conn->error);
    }
    $stmt->close();
    delete_files($ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/generated");
}

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
        <p class="title">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Profesores</span>
        </p>
        <p class="subtitle">Total: <?php echo(count($teachers_values));?></p>
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
                    <?php
                    // Get all values from teachers' table
                    foreach($teachers_values as $individual){
                        echo <<<EOL
                        <tr>
                            <td>$individual[0]</td>
                            <td>$individual[1]</td>
                            <td><a href='../getmedia.php?id=$individual[0]&media=picname&type=P' target='_blank'>$individual[2]</a></td>
                            <td><a href='../getmedia.php?id=$individual[0]&media=vidname&type=P' target='_blank'>$individual[3]</a></td>
                            <td><a href="$individual[4]" target="_blank">Abrir enlace</a></td>
                        EOL;
                        if(empty($individual[5])) {
                            echo("<td class='has-text-centered'>-</td>");
                        }
                        else {
                            echo <<<EOL
                            <td>$individual[5]</td>
                            EOL;
                        }
                        echo <<<EOL
                        <td>$individual[6]</td>
                        <td>$individual[7]</td>
                        </tr>
                        EOL;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <p class="title">
            <i class="fas fa-user-graduate"></i>
            <span>Alumnos</span>
        </p>
        <p class="subtitle">Total: <?php echo(count($students_values));?></p>
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
                    foreach($students_values as $value => $individual){
                        echo <<<EOL
                        <tr>
                            <td>$individual[0]</td>
                            <td>$individual[1]</td>
                            <td><a href='../getmedia.php?id=$individual[0]&media=picname&type=ALU' target='_blank'>$individual[2]</a></td>
                            <td><a href='../getmedia.php?id=$individual[0]&media=vidname&type=ALU' target='_blank'>$individual[3]</a></td>
                            <td><a href="$individual[4]" target="_blank">Abrir enlace</a></td>
                        EOL;
                        if(empty($individual[5])) {
                            echo("<td>-</td>");
                        }
                        else {
                            echo <<<EOL
                            <td>$individual[5]</td>
                            <td>$individual[6]</td>
                            </tr>
                            EOL;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <p class="title">
            <i class="far fa-images"></i>
            <span>Galería</span>
        </p>
        <p class="subtitle">Total: <?php echo(count($gallery_values));?></p>
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
                    foreach($gallery_values as $value => $individual){
                        echo <<<EOL
                        <tr>
                            <td>$individual[0]</td>
                            <td><a href='../getgallery.php?id=$individual[0]' target='_blank'>$individual[1]</a></td>
                            <td>$individual[2]</td>
                        </tr>
                        EOL;
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
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
                <a href="dashboard.php?deleteyearbook=true" class="button is-danger">
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
                <a href="dashboard.php?makeavailable=true" class="button is-link">
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