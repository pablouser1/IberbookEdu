<?php
require("helpers/common.php");
if (!extension_loaded('mysqli') || !extension_loaded('zip')) {
    die("Este programa necesita los siguientes plugins para funcionar: php-mysqli, php-zip");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db_config = $_POST["db"]; // DB data
    $global_config = $_POST["global"]; // Server-level config
    $owner = $_POST["owner"];
    $schoolinfo = $_POST["schoolinfo"];

    // DB connection info
    $db_file = '
    <?php
    $db_name = "'.$db_config[0].'";
    $db_host = "'.$db_config[1].'";
    $db_port = '.(int)$db_config[2].';
    $db_username = "'.$db_config[3].'";
    $db_password = "'.$db_config[4].'";
    ?>';
    file_put_contents("helpers/db_config.php", $db_file);

    // Config info
    if($global_config[0] == "andalucia"){
        $base_url = "https://seneca.juntadeandalucia.es/seneca/jsp/";
        $ssloptions = 'array(
            // The cafile is necessary only in Andalucia
            "cafile" => $base_path."helpers/cert/juntadeandalucia-es-chain.pem",
            "verify_peer"=> true,
            "verify_peer_name"=> true,
        );';
    }
    elseif($global_config[0] == "madrid"){
        $base_url = "https://raices.madrid.org/raiz_app/jsp/";
        $ssloptions = '
        array(
            "verify_peer"=> true,
            "verify_peer_name"=> true,
        );';
    }
    $global_config_file =
    '<?php
    // General
    $base_url = "'.$base_url.'"; // Remote server url
    $base_path = "'.dirname(__FILE__).'/"; // Program base dir
    $ybpath = "'.$global_config[1].'"; // Base dir for user uploads and generated yearbooks
    // Api
    $ssloptions = '.$ssloptions.'
    ?>';
    // Add global config file
    file_put_contents("helpers/config.php", $global_config_file);
    // Now that we have the config available, import helpers
    require_once("helpers/db.php");

    // Creating tables
    // Students
    $sql = "CREATE TABLE students(
        id int(8) not null UNIQUE,
        fullname varchar(255) not null,
        schoolid varchar(12) not null,
        schoolyear varchar(12) not null,
        picname varchar(255) not null,
        vidname varchar(255) not null,
        link varchar(255),
        quote varchar(280),
        uploaded DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating students' table: " . $conn->error);
    }

    // Teachers
    $sql = "CREATE TABLE teachers(
        id varchar(9) not null UNIQUE,
        fullname varchar(255) not null,
        schoolid varchar(12) not null,
        schoolyear varchar(12) not null,
        picname varchar(255) not null,
        vidname varchar(255) not null,
        link varchar(255),
        quote varchar(280),
        uploaded DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        subject varchar(24) not null,
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
        picdescription varchar(255),
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating gallery's table: " . $conn->error);
    }

    // Yearbook
    $sql = "CREATE TABLE `yearbooks` (
        `id` int NOT NULL,
        `schoolid` varchar(32) NOT NULL,
        `schoolyear` varchar(32) NOT NULL,
        `zipname` varchar(32) NOT NULL,
        `generated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `available` varchar(5) NOT NULL
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating yearbooks' table: " . $conn->error);
    }

    // Staff (admins and owner)
    $sql = "CREATE TABLE `staff` (
        `id` int NOT NULL AUTO_INCREMENT,
        `username` varchar(14) NOT NULL UNIQUE,
        `password` varchar(255),
        `permissions` varchar(14) NOT NULL,
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating admins' table: " . $conn->error);
    }

    // Schools
    $sql = "CREATE TABLE `schools` (
        `id` int NOT NULL UNIQUE,
        `name` varchar(128) NOT NULL,
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating admins' table: " . $conn->error);
    }
    
    // Writes data to DB

    // School info
    // First we need to get the school's name
    $stmt = $conn->prepare("INSERT INTO schools (id, name) VALUES (?, ?);");
    $stmt->bind_param("is", $schoolinfo[0], $schoolinfo[1]);
    if ($stmt->execute() !== true) {
        die("Error writing school: " . $conn->error);
    }
    $owner_password = password_hash($owner[1], PASSWORD_DEFAULT);
    // Staff info
    $stmt = $conn->prepare("INSERT INTO staff (username, password, permissions) VALUES  (?, ?, 'owner');");
    $stmt->bind_param("ss", $owner[0], $owner_password);
    if ($stmt->execute() !== true) {
        die("Error writing owners' info: " . $conn->error);
    }

    // Elimina setup
    unlink("assets/scripts/setup.js");
    unlink("setup.php");
    header("Location: index.html");
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
                        <input name="owner[]" id="password" class="input" type="password" placeholder="***********" required>
                    </div>
                </div>
                <hr>
                <div class="field">
                    <label class="label">Selecciona tu comunidad autónoma</label>
                    <div class="control">
                        <div class="select">
                            <select name="global[]" id="comunidadaut">
                                <option value="andalucia">Andalucía</option>
                                <option value="madrid">Madrid</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Ubicación de los yearbooks</label>
                    <div class="control">
                        <input name="global[]" id="yearbook" class="input" type="text" value="<?php echo(dirname(__FILE__)."/yearbooks/");?>" required>
                    </div>
                    <p class="help">Recomedable que <b>no</b> sea un directorio público</p>
                </div>
                <div class="field">
                    <h2 class="title">Centro admitido</h2>
                    <label class="label">ID del centro</label>
                    <div class="control">
                        <input name="schoolinfo[]" class="input" type="number" placeholder="Ej: 181206713" required>
                    </div>
                    <label class="label">Nombre del centro</label>
                    <div class="control">
                        <input name="schoolinfo[]" class="input" type="text" required>
                    </div>
                    <p class="help">Puedes conseguir la información en <a href="https://www.juntadeandalucia.es/educacion/vscripts/centros/index.asp" target="_blank">aquí</a> (Andalucia) o <a href="https://www.madrid.org/wpad_pub/run/j/MostrarConsultaGeneral.icm" target="_blank">aquí</a> (Madrid)</p>
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
    <script src="assets/scripts/setup.js"></script>
</body>

</html>
