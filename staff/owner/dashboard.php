<?php
session_start();
if (!isset($_SESSION["owner"])){
    http_response_code(401);
    echo("You don't have permissions");
    exit;
}

$ownerinfo = $_SESSION["ownerinfo"];
require_once("getprivinfo.php");

$info = new DBPrivInfo;
$staff = $info->staff();
$schools = $info->schools();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Owner Dashboard - IberbookEdu</title>
    <link rel="stylesheet" href="../../assets/styles/dashboard.css"/>
    <!-- Dev -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <script>
        // Initial vars
        const staff_js = <?php echo(json_encode($staff));?>;
        const schools_js = <?php echo(json_encode($schools));?>;
    </script>
</head>

<body>
    <div id="main">
        <!-- End navbar -->
        <section class="hero is-info welcome is-small">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title has-text-centered">
                        Welcome: <?php echo($ownerinfo["username"]);?>
                    </h1>
                </div>
            </div>
        </section>
        <div class="columns is-centered">
            <div class="column is-3">
                <aside class="menu is-hidden-mobile">
                    <p class="menu-label">General</p>
                    <ul class="menu-list">
                        <li><a :class="{'is-active': tab === 'mainmenu'}" v-on:click="changeTab('mainmenu')">Main menu</a></li>
                    </ul>
                    <p class="menu-label">Information</p>
                    <ul class="menu-list">
                        <li><a :class="{'is-active': tab === 'users'}" v-on:click="changeTab('users')">Uploads</a></li>
                    </ul>
                </aside>
            </div>
            <div class="column is-9">
                <section id="items" class="section">
                    <!-- Main Menu --->
                    <mainmenu v-if="tab === 'mainmenu'" v-bind:staff="staff" v-bind:schools="schools"></mainmenu>
                    <!-- User -->
                    <users v-if="tab === 'users'" v-bind:schools="schools"></users>
                </section>
            </div>
        </div>
    </div>
    <script src="../../assets/scripts/owner/dashboard.js"></script>
</body>

</html>
