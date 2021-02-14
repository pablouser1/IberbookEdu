<?php
if (!extension_loaded('mysqli') || !extension_loaded('zip')) {
    die("This app needs the following plugins to work: php-mysqli and php-zip");
}

// Default values
$dirs = [
    "upload" => __DIR__."/uploads/",
    "yearbook" => __DIR__."/yearbooks/"
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
    mkdir("config", 0750, true);
    file_put_contents("config/dbconf.php", $db_file);

    // Frontends setup
    $filtered_frontends = "";
    $amountFrontends = count($frontends);
    foreach ($frontends as $i => $frontend) {
        if (filter_var($frontend, FILTER_VALIDATE_URL)) {
            $filtered_frontends .= "'{$frontend}'";
        }
    }
    $global_config_file =
'<?php
// General
$uploadpath = "'.$global_config["uploaddir"].'"; // Uploads dir
$frontends = ['.$filtered_frontends.'];
$token_secret = "'.bin2hex(openssl_random_pseudo_bytes(32)).'"; // SECRET KEY, --> DO NOT SHARE <--
// Email
$email = [
    "enabled" => false
];
?>';
    // Add global config file
    file_put_contents("config/config.php", $global_config_file);
    // Now that we have the config available, import db helper
    require_once("helpers/db.php");
    $db = new DB;
    // Creating tables
    // Users
    $sql = "CREATE TABLE users(
        id INT NOT NULL AUTO_INCREMENT,
        username varchar(24) NOT NULL,
        `password` varchar(255) NOT NULL,
        `type` varchar(12) NOT NULL,
        `name` varchar(64) NOT NULL,
        `surname` VARCHAR(64) NOT NULL,
        schools TEXT NOT NULL,
        email varchar(255),
        voted int,
        primary key(id)
        )";
    if ($db->query($sql) !== TRUE) {
        die("Error creating users");
    }

    // Profiles
    $sql = "CREATE TABLE profiles(
        id INT NOT NULL AUTO_INCREMENT,
        userid INT NOT NULL,
        schoolid varchar(12) NOT NULL,
        schoolyear varchar(12) NOT NULL,
        photo varchar(255),
        video varchar(255),
        link varchar(255),
        quote varchar(280),
        uploaded DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `subject` varchar(24),
        PRIMARY KEY(id)
        )";
    if ($db->query($sql) !== TRUE) {
        die("Error creating profiles");
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
        `id` int NOT NULL AUTO_INCREMENT,
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
        die("Error creating groups");
    }

    // Themes
    $sql = "CREATE TABLE `themes` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(32) NOT NULL UNIQUE,
        PRIMARY KEY(id)
        )";
    if ($db->query($sql) !== TRUE) {
        die("Error creating themes");
    }

    // Messages
    $sql = "CREATE TABLE `messages` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `from` INT NOT NULL,
        `to` INT NOT NULL,
        `content` TEXT NOT NULL,
        `read` TINYINT(1) NOT NULL DEFAULT '0',
        `sent` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(id)
        )";
    
    // Writes data to DB

    // School info
    // First we need to get the school's name
    $stmt = $db->prepare("INSERT INTO schools (`name`) VALUES (?)");
    $stmt->bind_param("s", $schoolinfo["name"]);
    if ($stmt->execute() !== true) {
        die("Error writing school");
    }
    
    // Owner info
    $owner_password = password_hash($owner["password"], PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO staff (username, `password`, `permissions`) VALUES (?, ?, 'owner')");
    $stmt->bind_param("ss", $owner["username"], $owner_password);
    if ($stmt->execute() !== true) {
        die("Error writing owner");
    }

    // Default theme
    $sql = "INSERT INTO themes (`name`) VALUES ('default')";
    if ($db->query($sql) !== TRUE) {
        die("Error writing theme");
    }

    mkdir($global_config["uploaddir"], 0550);
    // Delete setup
    unlink("assets/scripts/setup.js");
    unlink("setup.php");
    die("Setup finished successfully");
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
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
</head>

<body>
    <section id="main">
        <section v-if="stage === 'splashscreen'" class="hero is-success is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title">
                        Welcome to IberbookEdu
                    </h1>
                    <h2 class="subtitle">
                        Let's start with some preparations...
                    </h2>
                    <p class="subtitle">
                        <button v-on:click="stage = 'database'" type="button" class="button is-link">Continue</button>
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
