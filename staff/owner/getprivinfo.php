<?php
// -- Get info from database -- //
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../../functions.php");
require_once("../../classes/groups.php");
require_once("../../classes/gallery.php");

if (isset($_GET["schoolid"], $_GET["schoolyear"])) {
    $groupClass = new Groups;
    $galleryClass = new Gallery;
    $users = $groupClass->getProfilesGroupFull($_GET["schoolid"], $_GET["schoolyear"]);
    $gallery = $galleryClass->getItems($_GET["schoolid"], $_GET["schoolyear"]);
    if (!empty($users)) {
        $response = [
            "code" => "C",
            "data" => [
                "users" => $users,
                "gallery" => $gallery
            ]
        ];
    }
    else {
        $response = [
            "code" => "E"
        ];
    }
    Utils::sendJSON($response);
}
?>
