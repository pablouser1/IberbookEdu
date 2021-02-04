<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../classes/profiles.php");

$auth = new Auth;
$profiles = new Profiles;

$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo && $auth->isUserAdmin($userinfo)) {
    if (isset($_POST["id"], $_POST["elements"])) {
        if ($profiles->deleteProfileItems($_POST["id"], $_POST["elements"], $profileinfo["schoolid"], $profileinfo["year"])) {
            $response = [
                "code" => "C"
            ];
        }
        else {
            $response = [
                "code" => "E",
                "error" => "There was an error while deleting some elements"
            ];
        }
    }
    else {
        $response = [
            "code" => "E",
            "error" => "Not enough info provided"
        ];
    }
}
else {
    http_response_code(401);
    $response = [
        "code" => "E",
        "error" => "Not logged in as admin"
    ];
}

Utils::sendJSON($response);
?>
