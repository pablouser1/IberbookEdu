<?php
session_start();
require_once("helpers/db/db.php");
require_once("helpers/config.php");
$db = new DB;
$yearbooks = array();
$leaderboards = array();

$sql = "SELECT id, schoolid, schoolname, schoolyear, acyear, banner, voted FROM yearbooks ORDER BY voted DESC";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $yearbooks[] = [
            "id" => $row["id"],
            "schoolid" => $row["schoolid"],
            "schoolname" => $row["schoolname"],
            "schoolyear" => $row["schoolyear"],
            "acyear" => $row["acyear"],
            "zip" => $ybpath.$row["id"]."/yearbook.zip",
            "link" => $ybpath.$row["id"],
            "banner" => $ybpath.$row["id"]."/assets/".$row["banner"],
            "votes" => (int)$row["voted"]
        ];
    }
}

// Login options
if (isset($_SESSION["loggedin"])) {
    $userinfo = $_SESSION["userinfo"];
    $stmt = $db->prepare("SELECT voted FROM users WHERE id=?");
    $stmt->bind_param("i", $userinfo["iduser"]);
    $stmt->execute();
    $stmt->bind_result($votedid);
    $stmt->fetch();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orlas - IberbookEdu</title>
    <!-- Dev -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> -->
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
        const yearbooks_js = <?php echo(json_encode($yearbooks)); ?>;
        // Yearbook id from database info (user voted yearbook)
        const voted_js = <?php echo (!isset($votedid)) ? 'null' : (int)$votedid; ?>;
    </script>
</head>

<body>
    <section id="yearbooks" class="hero is-fullheight">
        <div class="hero-head">
            <header id="navbar" class="navbar" role="navigation" aria-label="main navigation">
                <div class="navbar-brand">
                    <a href="index.php" class="navbar-item">
                        <span class="icon">
                            <i class="fas fa-home"></i>
                        </span>
                        <span><b>Inicio</b></span>
                    </a>
                    <a class="navbar-burger" :class="{ 'is-active': showNav }" @click="showNav = !showNav" role="button" aria-label="menu" aria-expanded="false">
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                        <span aria-hidden="true"></span>
                    </a>
                </div>
                <div class="navbar-menu" :class="{ 'is-active': showNav }">
                    <div class="navbar-end">
                        <?php
                        // If user is logged in
                        if (isset($_SESSION["loggedin"])) {
                            echo '
                            <a class="navbar-item" href="users/dashboard.php">
                                <span class="icon">
                                    <i class="fas fa-user"></i>
                                </span>
                                <span><b>Panel de control</b></span>
                            </a>
                            ';
                        }
                        // User is not logged in
                        else {
                            echo '
                            <a class="navbar-item" href="login.php">
                                <span class="icon">
                                    <i class="fas fa-user-circle"></i>
                                </span>
                                <span><b>Iniciar sesión</b></span>
                            </a>
                            ';
                        }
                        ?>
                        <a class="navbar-item" href="about.html">
                            <span class="icon">
                                <i class="fas fa-info-circle"></i>
                            </span>
                            <span><b>Acerca de</b></span>
                        </a>
                    </div>
                </div>
            </header>
            <!-- Search bar -->
            <search></search>
        </div>
        <div class="hero-body">
            <!-- Public yearbooks -->
            <yearbooks v-bind:yearbooks="yearbooks"></yearbooks>
        </div>
        <div class="hero-foot">
            <p class="has-text-centered">Hecho con ❤️ en Github</p>
        </div>
    </section>
    <script src="assets/scripts/yearbooks.js"></script>
</body>

</html>
