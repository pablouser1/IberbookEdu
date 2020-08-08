<?php
require("helpers/common.php");
if (!extension_loaded('mysqli') || !extension_loaded('zip')) {
    die("Este programa necesita los siguientes plugins para funcionar: php-mysqli, php-zip");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db_config = $_POST["db"]; // DB data
    $owner = $_POST["owner"];
    $allowed_school = test_input($_POST["allowed_school"]);
    // DB connection info
    $config_data =
    '
    <?php
    $db_name = "'.$db_config[0].'";
    $host = "'.$db_config[1].'";
    $port = '.(int)$db_config[2].';
    $username = "'.$db_config[3].'";
    $password = "'.$db_config[4].'";
    ?>';
    file_put_contents("helpers/db_config.php", $config_data);

    // Now that we have the config available, import database helper
    require_once("helpers/db.php");

    // Creating tables
    // Students
    $sql = "CREATE TABLE students(
        id int(10) not null UNIQUE,
        fullname varchar(255) not null,
        schoolid varchar(10) not null,
        schoolyear varchar(56) not null,
        picname varchar(255) not null,
        vidname varchar(255) not null,
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating students' table: " . $conn->error);
    }

    // Teachers
    $sql = "CREATE TABLE teachers(
        id varchar(10) not null,
        fullname varchar(255) not null,
        schoolid varchar(10) not null,
        schoolyear varchar(10) not null,
        picname varchar(255) not null,
        vidname varchar(255) not null,
        subject varchar(12) not null
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating teachers' table: " . $conn->error);
    }

    // Gallery
    $sql = "CREATE TABLE gallery(
        id int not null auto_increment,
        picname varchar(255) not null,
        schoolid varchar(10) not null,
        schoolyear varchar(10) not null,
        picdescription varchar(255) not null,
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating gallery's table: " . $conn->error);
    }

    // Yearbook
    $sql = "CREATE TABLE `yearbooks` (
        `id` int(11) NOT NULL,
        `schoolid` varchar(32) NOT NULL,
        `schoolyear` varchar(32) NOT NULL,
        `zipname` varchar(32) NOT NULL,
        `generated` datetime NOT NULL,
        `available` tinyint(1) NOT NULL
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating yearbooks' table: " . $conn->error);
    }

    // Staff (admins and owner)
    $sql = "CREATE TABLE `staff` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(14) NOT NULL UNIQUE,
        `password` varchar(256),
        `permissions` varchar(14) NOT NULL,
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating admins' table: " . $conn->error);
    }

    // Schools
    $sql = "CREATE TABLE `schools` (
        `id` int(8) NOT NULL,
        `name` varchar(128) NOT NULL
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating admins' table: " . $conn->error);
    }
    
    // Writes data to DB

    // School info
    $stmt = $conn->prepare("INSERT INTO schools (id, name) VALUES  (?, ?);");
    $placeholder = 2208;
    $stmt->bind_param("is", $placeholder, $allowed_school);
    if ($stmt->execute() !== true) {
        die("Error writing school: " . $conn->error);
    }

    // Staff info
    $stmt = $conn->prepare("INSERT INTO staff (username, password, permissions) VALUES  (?, ?, owner);");
    $stmt->bind_param("s", test_input($owner[0]), $owner[1]);
    if ($stmt->execute() !== true) {
        die("Error writing owners' info: " . $conn->error);
    }

    // Elimina setup
    //unlink("scripts/setup.php");
    //unlink("setup.php");
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yearbook Setup</title>
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css" />
</head>

<body>
    <section id="welcome" class="hero is-success is-fullheight">
        <div class="hero-body">
            <div class="container">
                <h1 class="title animate__animated animate__fadeInDown">
                    Bienvenido a Iberbook
                </h1>
                <h2 class="subtitle animate__animated animate__fadeInUp">
                    Vamos a empezar con las preparaciones...
                </h2>
                <p class="subtitle animate__animated animate__fadeInUp">
                    <button id="preparations" type="button" class="button is-link">Continuar</button>
                </p>
            </div>
        </div>
    </section>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <section id="database" class="section is-hidden animate__animated animate__bounceInLeft">
            <div class="container">
                <div class="section">
                    <h1 class="title">Base de datos</h1>
                    <hr>
                    <div class="field">
                        <label class="label">Nombre de la base de datos</label>
                        <div class="control">
                            <input name="db[]" id="name" class="input" type="text" placeholder="Ej: iberbook_db" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Host</label>
                        <div class="control">
                            <input name="db[]" id="host" class="input" type="text" placeholder="Ej: localhost" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Puerto</label>
                        <div class="control">
                            <input name="db[]" id="port" class="input" type="text" value="3306" required>
                        </div>
                        <p class="help">Por detecto es 3306</p>
                    </div>
                    <div class="field">
                        <label class="label">Nombre de usuario</label>
                        <div class="control">
                            <input name="db[]" id="username" class="input" type="text" placeholder="Ej: usuario" required>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Contraseña</label>
                        <div class="control">
                            <input name="db[]" id="password" class="input" type="password" placeholder="**********" required>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button id="database_next" type="button" class="button is-success">
                                <span class="icon">
                                    <i class="fas fa-forward"></i>
                                </span>
                                <span>Siguiente</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="owner" class="section is-hidden animate__animated animate__bounceInDown">
            <div class="container">
                <h1 class="title">Datos del servidor</h1>
                <hr>
                <h2 class="title">Cuenta del dueño</h2>
                <h2 class="subtitle">Esta cuenta tendrá los máximos permisos posibles</h2>
                <div class="field">
                    <label class="label">Nombre de usuario</label>
                    <div class="control">
                        <input name="owner[]" id="username" class="input" type="text" placeholder="Ej: usuario" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Contraseña</label>
                    <div class="control">
                        <input name="owner[]" id="password" class="input" type="text" placeholder="Ej: usuario" required>
                    </div>
                </div>
                <h2 class="title">Centros admitidos</h2>
                <div class="field">
                    <label class="label">Nombre del centro</label>
                    <div class="control">
                        <input name="allowed_school" class="input" type="text" placeholder="Ej: I.E.S Al-Baytar" required>
                    </div>
                    <p class="help"><b class="has-text-danger">ADVERTENCIA</b>: El nombre del centro tiene que escribirse <u><b>de la misma manera</b></u> de la que sale en PASEN/SENECA</p>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button id="database_back" type="button" class="button is-info">
                            <span class="icon">
                                <i class="fas fa-backward"></i>
                            </span>
                            <span>Atrás</span>
                        </button>
                    </div>
                    <div class="control">
                        <button id="sendall" type="submit" class="button is-success">
                            <span class="icon">
                                <i class="fas fa-paper-plane"></i>
                            </span>
                            <span>Enviar todo</span>
                        </button>
                    </div>
                </div>
                <progress id="progress" class="progress is-primary is-hidden" max="100"></progress>
            </div>
        </section>
    </form>
    <script src="scripts/setup.js"></script>
</body>

</html>