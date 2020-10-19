<?php
session_start();
require_once("helpers/db/db.php");
require_once("helpers/config.php");
$db = new DB;
$yearbooks = array();
$leaderboards = array();
$sql = "SELECT id, schoolid, schoolname, schoolyear, zipname, acyear, voted FROM yearbooks ORDER BY schoolyear ASC";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Year without spaces (used for URLs)
        $yearuser = str_replace(' ', '', $row["schoolyear"]);
        $yearbooks[$row["schoolid"]]["schoolname"] = $row["schoolname"];
        $yearbooks[$row["schoolid"]]["acyears"][$row["acyear"]][$row["schoolyear"]] = [
            "id" => $row["id"],
            "zip" => $ybpath.$row["schoolid"]."/".$row["acyear"]."/".$yearuser."/".$row["zipname"],
            "link" => $ybpath.$row["schoolid"]."/".$row["acyear"]."/".$yearuser,
            "votes" => (int)$row["voted"]
        ];
    }
    $sql = "SELECT id, schoolid, schoolname, schoolyear, acyear, voted FROM yearbooks ORDER BY voted DESC LIMIT 5";
    $result = $db->query($sql);
    while($row = $result->fetch_assoc()) {
        $yearuser = str_replace(' ', '', $row["schoolyear"]);
        $leaderboards[] = [
            "schoolname" => $row["schoolname"],
            "schoolid" => $row["schoolid"],
            "schoolyear" => $row["schoolyear"],
            "link" => $ybpath.$row["schoolid"]."/".$row["acyear"]."/".$yearuser,
            "acyear" => $row["acyear"],
            "voted" => $row["voted"]
        ];
    }
}
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
    <title>Yearbooks - IberbookEdu</title>
    <!-- Dev -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
        const yearbooks_js = <?php echo(json_encode($yearbooks)); ?>;
        const leaderboards_js = <?php echo(json_encode($leaderboards)); ?>;
        // Yearbook id from database info (user voted yearbook)
        const voted_js = <?php echo (!isset($votedid)) ? 'null' : (int)$votedid; ?>;
    </script>
</head>

<body class="has-navbar-fixed-top">
    <div id="yearbooks">
        <nav id="navbar" class="navbar is-primary is-bold is-fixed-top" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a href="index.php" class="navbar-item">
                    <b>IberbookEdu</b>
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
                    <a class="navbar-item" href="yearbooks.php">
                        <span class="icon">
                            <i class="fas fa-book"></i>
                        </span>
                        <span><b>Yearbooks</b></span>
                    </a>
                    <a class="navbar-item" href="about.html">
                        <span class="icon">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <span><b>Acerca de</b></span>
                    </a>
                </div>
            </div>
        </nav>
        <section class="section">
            <!-- Centros y curso académico -->
            <schools v-bind:schools="schools"></schools>
            <hr>
            <!-- Grupos -->
            <groups v-bind:groups="groups" v-bind:groupsextra="groupsextra"></groups>
            <!-- NoScript Alert -->
            <noscript>Esta página neceista Javascript para funcionar</noscript>
            <hr>
            <!-- Yearbook -->
            <yearbook v-bind:yearbook="yearbook" v-bind:yearbookextra="yearbookextra"></yearbook>
        </section>
        <section class="section">
            <p class="title has-text-centered">
                <i class="fas fa-trophy"></i>
                <span>Leaderboards</span>
            </p>
            <p class="subtitle has-text-centered">
                <i class="fas fa-star"></i>
                <span>Las 5 orlas más votadas de todos los tiempos</span>
            </p>
            <leaderboards v-bind:leaderboards="leaderboards"></leaderboards>
        </section>
        <footer class="footer">
            <div class="content has-text-centered">
                <a href="about.html">Acerca de</a>
            </div>
            <div class="content has-text-centered">
                Hecho con <span style='color: #e25555;'> &#9829; </span> en Github
            </div>
        </footer>
    </div>
    <script src="assets/scripts/yearbooks.js"></script>
</body>

</html>
