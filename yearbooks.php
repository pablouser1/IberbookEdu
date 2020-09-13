<?php
require_once("helpers/db/db.php");
require_once("helpers/config.php");

$yearbooks = array();
$sql = "SELECT schoolid, schoolname, schoolyear, zipname, acyear FROM yearbooks";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Year without spaces (used for URLs)
        $yearuser = str_replace(' ', '', $row["schoolyear"]);
        $yearbooks[$row["schoolid"]]["schoolname"] = $row["schoolname"];
        $yearbooks[$row["schoolid"]]["acyears"][$row["acyear"]][$row["schoolyear"]] = [
            "zip" => $ybpath.$row["schoolid"]."/".$row["acyear"]."/".$yearuser."/".$row["zipname"],
            "link" => $ybpath.$row["schoolid"]."/".$row["acyear"]."/".$yearuser
        ];
    }
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
    <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css"/>
    <script>
        const yearbooks_js = <?php echo(json_encode($yearbooks)); ?>;
    </script>
</head>

<body>
    <!-- Banner -->
    <section class="hero is-primary is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">Yearbooks</h1>
                <h2 class="subtitle">IberbookEdu</h2>
            </div>
        </div>
    </section>
    <section id="yearbooks" class="section">
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
    <footer class="footer">
        <div class="content has-text-centered">
            <a href="about.html">Acerca de</a>
        </div>
        <div class="content has-text-centered">
            Hecho con <span style='color: #e25555;'> &#9829; </span> en Github
        </div>
    </footer>
    <script src="assets/scripts/yearbooks.js"></script>
</body>

</html>

