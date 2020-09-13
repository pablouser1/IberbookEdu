<?php
if (!extension_loaded('mysqli') || !extension_loaded('zip') || !extension_loaded('curl')) {
    die("Este programa necesita los siguientes plugins para funcionar: php-mysqli, php-zip y php-curl");
}

$dirs = [
    "upload" => dirname(__FILE__)."/uploads/",
    "yearbook" => dirname($_SERVER['PHP_SELF'])."/yearbooks/"
];

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
    file_put_contents("helpers/db/dbconf.php", $db_file);

    // Config info
    if($global_config[0] == "andalucia"){
        $base_url = "https://seneca.juntadeandalucia.es/seneca/jsp/";
        $ssloptions = 'array(
            // The cafile is necessary only in Andalucia
            CURLOPT_CAINFO => $base_path."helpers/cert/juntadeandalucia-es-chain.pem",
            CURLOPT_SSL_VERIFYPEER => true
        );';
    }
    elseif($global_config[0] == "madrid"){
        $base_url = "https://raices.madrid.org/raiz_app/jsp/";
        $ssloptions = '
        array(
            CURLOPT_SSL_VERIFYPEER => true
        );';
    }
    $global_config_file =
    '<?php
    // General
    $base_url = "'.$base_url.'"; // Remote server url
    $base_path = "'.dirname(__FILE__).'/"; // Program base dir
    $uploadpath = "'.$global_config[1].'"; // Uploads dir
    $ybpath = "'.$global_config[2].'"; // Base dir for user uploads and generated yearbooks
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
        photo varchar(255) not null,
        video varchar(255) not null,
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
        photo varchar(255) not null,
        video varchar(255) not null,
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
        name varchar(255) not null,
        schoolid varchar(10) not null,
        schoolyear varchar(10) not null,
        description varchar(255),
        type varchar(8) not null,
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating gallery's table: " . $conn->error);
    }

    // Yearbook
    $sql = "CREATE TABLE `yearbooks` (
        `id` int NOT NULL AUTO_INCREMENT,
        `schoolid` varchar(32) NOT NULL,
        `schoolyear` varchar(32) NOT NULL,
        `zipname` varchar(32) NOT NULL,
        `generated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `acyear` varchar(16) NOT NULL,
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating yearbooks' table: " . $conn->error);
    }

    // Staff (admins and owner)
    $sql = "CREATE TABLE `staff` (
        `id` int NOT NULL AUTO_INCREMENT,
        `username` varchar(14) NOT NULL UNIQUE,
        `password` varchar(80) NOT NULL,
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
        `url` varchar(255),
        primary key(id)
        )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating admins' table: " . $conn->error);
    }
    
    // Writes data to DB

    // School info
    // First we need to get the school's name
    $stmt = $conn->prepare("INSERT INTO schools (id, url) VALUES (?, ?);");
    $stmt->bind_param("is", $schoolinfo[0], $schoolinfo[1]);
    if ($stmt->execute() !== true) {
        die("Error writing school: " . $conn->error);
    }
    
    // Staff info
    $owner_password = password_hash($owner[1], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO staff (username, password, permissions) VALUES  (?, ?, 'owner');");
    $stmt->bind_param("ss", $owner[0], $owner_password);
    if ($stmt->execute() !== true) {
        die("Error writing owners' info: " . $conn->error);
    }
    mkdir($global_config[1], 0755);
    // Elimina setup
    unlink("assets/scripts/setup.js");
    unlink("setup.php");
    header("Location: index.php");
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
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css" />
</head>

<body>
    <section id="main">
        <section v-if="$root.stage === 'splashscreen'" class="hero is-success is-fullheight">
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
                <noscript>Este programa necesita Javascript</noscript>
                <database v-if="$root.stage === 'database'"></database>
                <owner v-if="$root.stage === 'owner'"></owner>
                <server v-bind:dirs="dirs" v-if="$root.stage === 'server'"></server>
            </div>
        </form>
    </section>
    <script src="assets/scripts/setup.js"></script>
</body>

</html>

