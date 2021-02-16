<?php
require_once("headers.php");
require_once("functions.php");
require_once("auth.php");
require_once("classes/users.php");
$auth = new Auth;
$userinfo = $auth->isUserLoggedin();
if ($userinfo) {
    $users = new Users;
    if (isset($_POST["oldPassword"], $_POST["newPassword"])) {
        $oldPassword = trim($_POST["oldPassword"]);
        $newPassword = trim($_POST["newPassword"]);
        if ($users->changePassword($userinfo["id"], $oldPassword, $newPassword)) {
            $response = [
                "code" => "C"
            ];
        }
        else {
            $response = [
                "code" => "E",
                "error" => "Error when changing password"
            ];
        }
    }
    else {
        $response = [
            "code" => "E",
            "error" => "Incomplete data"
        ];
    }
    Utils::sendJSON($response);
}
?>
