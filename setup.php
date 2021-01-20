<?php
if (!extension_loaded('mysqli') || !extension_loaded('zip') || !extension_loaded('curl')) {
    die("Este programa necesita los siguientes plugins para funcionar: php-mysqli, php-zip y php-curl");
}

// Default values
$dirs = [
    "upload" => dirname(__FILE__)."/uploads/",
    "yearbook" => dirname($_SERVER['PHP_SELF'])."/yearbooks/"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db_config = $_POST["db"]; // DB data
    $global_config = $_POST["global"]; // Server-level config
    $owner = $_POST["owner"];
    $schoolinfo = $_POST["schoolinfo"];
    $frontends = $_POST["frontends"];

    // DB connection info
    $db_file = '
    <?php
    $db_name = "'.$db_config["name"].'";
    $db_host = "'.$db_config["host"].'";
    $db_port = '.(int)$db_config["port"].';
    $db_username = "'.$db_config["username"].'";
    $db_password = "'.$db_config["password"].'";
    ?>';
    file_put_contents("helpers/db/dbconf.php", $db_file);

    // Config info
    switch ($global_config["login"]) { // Login system used
        case "andalucia":
            $login = "ced";
            $base_url = "https://seneca.juntadeandalucia.es/seneca/jsp/";
            $ssloptions = 'array(
                // The cafile is necessary only in Andalucia
                CURLOPT_CAINFO => __DIR__."/cert/juntadeandalucia-es-chain.pem",
                CURLOPT_SSL_VERIFYPEER => true
            );';
        break;
        case "madrid":
            $login = "ced";
            $base_url = "https://raices.madrid.org/raiz_app/jsp/";
            $ssloptions = '
            array(
                CURLOPT_SSL_VERIFYPEER => true
            );';
        break;
        case "local":
            $login = "local";
        break;
        default:
        die("Has elegido un sistema de login incorrecto");
    }
    // Frontends setup
    $filtered_frontends = [];
    foreach ($frontends as $frontend) {
        if (filter_var($frontend, FILTER_VALIDATE_URL)) {
            array_push($filtered_frontends, $frontend);
        }
    }
    $global_config_file =
'<?php
// General
$login = "'.$login.'"; // Login system used
$base_url = "'.$base_url.'"; // Remote server url
$uploadpath = "'.$global_config["uploaddir"].'"; // Uploads dir
$frontends = '.$filtered_frontends.';
$token_secret = "'.md5(uniqid(rand(), true)).'"; // TOKEN SECRET, --> DO NOT SHARE <--
// Api
$ssloptions = '.$ssloptions.';
?>';
    // Add global config file
    file_put_contents("helpers/config.php", $global_config_file);
    // Now that we have the config available, import db helper
    require_once("helpers/db/db.php");
    $db = new DB;
    // Creating tables
    // Users
    $sql = "CREATE TABLE users(
        id INT NOT NULL AUTO_INCREMENT,
        userid varchar(9),
        `type` varchar(12),
        fullname varchar(255) NOT NULL,
        schoolid varchar(12) NOT NULL,
        schoolyear varchar(12) NOT NULL,
        email varchar(255),
        photo varchar(255),
        video varchar(255),
        link varchar(255),
        quote varchar(280),
        uploaded DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `subject` varchar(24),
        voted int,
        primary key(id)
        )";
    if ($db->query($sql) !== TRUE) {
        die("Error creating users");
    }

    // Gallery
    $sql = "CREATE TABLE gallery(
        id int not null auto_increment,
        name varchar(255) not null,
        schoolid varchar(10) not null,
        schoolyear varchar(10) not null,
        description varchar(255),
        type varchar(8) not null,
        primary key(id)
        )";
    if ($db->query($sql) !== TRUE) {
        die("Error creating gallery");
    }

    // Yearbook
    $sql = "CREATE TABLE `yearbooks` (
        `id` int NOT NULL AUTO_INCREMENT,
        `schoolid` varchar(32) NOT NULL,
        `schoolyear` varchar(32) NOT NULL,
        `generated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `acyear` varchar(16) NOT NULL,
        `banner` varchar(30),
        `voted` int DEFAULT '0',
        primary key(id)
        )";
    if ($db->query($sql) !== TRUE) {
        die("Error creating yearbooks");
    }

    // Staff (admins and owner)
    $sql = "CREATE TABLE `staff` (
        `id` int NOT NULL AUTO_INCREMENT,
        `username` varchar(14) NOT NULL UNIQUE,
        `password` varchar(80) NOT NULL,
        `permissions` varchar(14) NOT NULL,
        primary key(id)
        )";
    if ($db->query($sql) !== TRUE) {
        die("Error creating staff");
    }

    // Schools
    $sql = "CREATE TABLE `schools` (
        `id` int NOT NULL UNIQUE,
        `name` varchar(128) NOT NULL,
        primary key(id)
        )";
    if ($db->query($sql) !== TRUE) {
        die("Error creating schools");
    }

    // Groups
    $sql = "CREATE TABLE `groups` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` varchar(32) NOT NULL UNIQUE,
        primary key(id)
        )";
    if ($db->query($sql) !== TRUE) {
        die("Error creating schools");
    }
    // Writes data to DB

    // School info
    // First we need to get the school's name
    $stmt = $db->prepare("INSERT INTO schools (id. `name`) VALUES (?, ?);");
    $stmt->bind_param("is", $schoolinfo["id"], $schoolinfo["name"]);
    if ($stmt->execute() !== true) {
        die("Error writing school");
    }
    
    // Staff info
    $owner_password = password_hash($owner["password"], PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO staff (username, password, permissions) VALUES  (?, ?, 'owner');");
    $stmt->bind_param("ss", $owner["username"], $owner_password);
    if ($stmt->execute() !== true) {
        die("Error writing owner");
    }
    mkdir($global_config["uploaddir"], 0755);
    // Elimina setup
    unlink("assets/scripts/setup.js");
    unlink("setup.php");
    echo("Setup realizado con éxito");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yearbook Setup</title>
    <script>
        const dirs = <?php echo json_encode($dirs); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css" />
</head>

<body>
    <section id="main">
        <section v-if="stage === 'splashscreen'" class="hero is-success is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title animate__animated animate__fadeInDown">
                        Bienvenido a Iberbook
                    </h1>
                    <h2 class="subtitle animate__animated animate__fadeInUp">
                        Vamos a empezar con las preparaciones...
                    </h2>
                    <p class="subtitle animate__animated animate__fadeInUp">
                        <button v-on:click="stage = 'database'" type="button" class="button is-link">Continuar</button>
                    </p>
                </div>
            </div>
        </section>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="container">
                <noscript>This app needs Javascript</noscript>
                <database v-show="stage === 'database'"></database>
                <owner v-show="stage === 'owner'"></owner>
                <server v-bind:dirs="dirs" v-show="stage === 'server'"></server>
            </div>
        </form>
    </section>
    <script src="assets/scripts/setup.js"></script>
</body>

</html>
