<?php
require_once("headers.php");
require_once("functions.php");
require_once("auth.php");
require_once("config/config.php");
$auth = new Auth;

$loggedin = false;
if ($userinfo = $auth->isUserLoggedin()) {
    $loggedin = true;
}

$response = [
    "code" => "C",
    "data" => [
        "title" => $server["name"],
        "description" => $server["description"],
        "loggedin" => $loggedin
    ]
];
sendJSON($response);
?>
