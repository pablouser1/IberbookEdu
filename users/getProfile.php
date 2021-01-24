<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../lang/lang.php");
require_once("../classes/profiles.php");

$auth = new Auth;
$profiles = new Profiles;
if ($profileinfo = $auth->isProfileLoggedin()) {
    $profile = $profiles->getProfile($profileinfo["id"]);
    if ($profile) {
        $response = [
            "code" => "C",
            "data" => $profile
        ];
    }
    else {
        $response = [
            "code" => "E",
            "error" => L::user_notExist
        ];
    }
    Utils::sendJSON($response);
}
?>
