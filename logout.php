<?php
require_once("functions.php");
require_once("headers.php");
require_once("helpers/db.php");
require_once("auth.php");

$db = new DB;
$auth = new Auth;
$userinfo = $auth->isUserLoggedin();

if ($userinfo) {
    unset($_COOKIE["user"]);
    unset($_COOKIE["profile"]);
    setcookie("user", "", [
        'expires' => time()-86400,
        'httponly' => true,
        'secure' => true
    ]);
    setcookie("profile", "", [
        'expires' => time()-86400,
        'httponly' => true,
        'secure' => true
    ]);
}
$response = [
    "code" => "C"
];
Utils::sendJSON($response);
?>
