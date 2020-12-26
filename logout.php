<?php
require_once("functions.php");
require_once("headers.php");
require_once("helpers/db.php");
require_once("auth.php");

$db = new DB;
$auth = new Auth;
$userinfo = $auth->isUserLoggedin();

if ($userinfo) {
    unset($_COOKIE["login"]);
    setcookie("login", "", [
        'expires' => time()-86400,
        'httponly' => true,
        'secure' => true
    ]);
    $response = [
        "code" => "C"
    ];
}
else {
    $response = [
        "code" => "E",
        "error" => "No has iniciado sesión"
    ];
}
sendJSON($response);
?>
