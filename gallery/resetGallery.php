<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");
require_once("../config/config.php");
require_once("../lang/lang.php");

$auth = new Auth;
$db = new DB;
$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo && $auth->isUserAdmin($userinfo)) {
    $stmt = $db->prepare("DELETE FROM gallery WHERE schoolid=? AND schoolyear=?");
    $stmt->bind_param("is", $profileinfo["schoolid"], $profileinfo["year"]);
    if ($stmt->execute()) {
        // TODO, CHECK IF FILES ARE ACTUALLY BEING REMOVED
        Utils::recursiveRemove($uploadpath."/".$profileinfo["schoolid"]."/".$profileinfo["year"]."/gallery/");
        $response = [
            "code" => "C"
        ];
    }
    else {
        $response = [
            "code" => "E",
            "error" => "Error deleting gallery from database"
        ];
    }
}
else {
    http_response_code(401);
    $response = [
        "code" => "E",
        "error" => "Bad auth"
    ];
}
Utils::sendJSON($response);
?>
