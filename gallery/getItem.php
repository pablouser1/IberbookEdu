<?php
// Get item of gallery
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../classes/gallery.php");

$auth = new Auth;

if ($profileinfo = $auth->isProfileLoggedin()) {
    $gallery = new Gallery;
    if (isset($_GET["id"])) {
        $gallery->streamItem($_GET["id"], $profileinfo["schoolid"], $profileinfo["year"]);
    }
    else {
        die("ID not supplied");
    }
}
else {
    http_response_code(401);
    die("Not logged in");
}

?>
