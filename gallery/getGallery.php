<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../classes/gallery.php");

$auth = new Auth;

$profileinfo = $auth->isProfileLoggedin();
if ($profileinfo) {
    $gallery = new Gallery;
    $items = $gallery->getItems($profileinfo["schoolid"], $profileinfo["year"]);

    $response = [
        "code" => "C",
        "data" => $items
    ];
    Utils::sendJSON($response);
}
?>
