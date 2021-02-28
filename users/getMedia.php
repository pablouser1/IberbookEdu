<?php
// Get pic and vid of user
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../classes/users.php");
require_once("../classes/profiles.php");

switch ($_GET["media"]) {
    case "photo":
    case "video":
        $media = $_GET["media"];
    break;
    default:
        die("That file type doesn't exist");
}

$auth = new Auth;
$profiles = new Profiles;
$users = new Users;

$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo) {
    $downloadable = false;
    // Check first if user can download
    if ($_GET["id"] == $profileinfo["id"] || $auth->isUserAdmin($userinfo)) {
        $profile = $profiles->getProfile($_GET["id"]);
        $user = $users->getUser($profile["userid"]);
        if ($profile && $user && isset($profile[$media]) && $profileinfo["schoolid"] == $profile["schoolid"] && $profileinfo["year"] === $profile["schoolyear"]) {
            $mediaid = $profile["id"];
            $medianame = $profile[$media];
            $downloadable = true;
        }
    }

    if ($downloadable) {
        // Stream file
        $profiles->streamMedia($profile["schoolid"], $profile["schoolyear"], $mediaid, $medianame);
    }
    else {
        echo("You can't download that");
        http_response_code(401);
        exit;
    }
}
else {
    echo("You aren't logged in");
    http_response_code(401);
    exit;
}
?>
