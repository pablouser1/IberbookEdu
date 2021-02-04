<?php
session_start();
if (!isset($_SESSION["owner"])){
    http_response_code(401);
    echo("You don't have permissions");
    exit;
}

$ownerinfo = $_SESSION["ownerinfo"];
require_once("../../classes/staff.php");
require_once("../../classes/schools.php");
require_once("../../classes/groups.php");
require_once("../../classes/themes.php");
require_once("../../classes/users.php");

// Get staff
$staffClass = new Staff;
$staff = $staffClass->getStaff();

// Get schools
$schoolsClass = new Schools;
$schools = $schoolsClass->getSchools();

// Get groups
$groupsClass = new Groups;
$groups = $groupsClass->getGroups();

// Get themes
$themesClass = new Themes;
$themes = $themesClass->getThemes();

$usersClass = new Users;
$users = $usersClass->getAllUsers();
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
        const groups_js = <?php echo(json_encode($groups));?>;
        const themes_js = <?php echo(json_encode($themes));?>;
        const users_js = <?php echo(json_encode($users));?>;
    </script>
</head>

<body>
    <div id="main">
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
                        <li><a :class="{'is-active': tab === 'mainmenu'}" @click="changeTab('mainmenu')">Main menu</a></li>
                    </ul>
                    <p class="menu-label">Manage</p>
                    <ul class="menu-list">
                        <li><a :class="{'is-active': tab === 'themes'}" @click="changeTab('themes')">Themes</a></li>
                    </ul>
                </aside>
            </div>
            <div class="column is-9">
                <section id="items" class="section">
                    <!-- Main Menu --->
                    <mainmenu v-if="tab === 'mainmenu'" :staff="staff" :schools="schools" :groups="groups" :users="users"></mainmenu>
                    <!-- Themes -->
                    <themes v-if="tab === 'themes'" v-bind:themes="themes"></themes>
                </section>
            </div>
        </div>
    </div>
    <script src="../../assets/scripts/owner/dashboard/modals.js"></script>
    <script src="../../assets/scripts/owner/dashboard/mainmenu.js"></script>
    <script src="../../assets/scripts/owner/dashboard/themes.js"></script>
    <script src="../../assets/scripts/owner/dashboard/dashboard.js"></script>
</body>

</html>
