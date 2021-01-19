<?php
require_once("../../headers.php");
require_once("../../functions.php");
require_once("../../auth.php");
require_once("themes.php");
require_once("../../lang/lang.php");
$auth = new Auth;

$userinfo = $auth->isUserLoggedin();
if ($userinfo && $auth->isUserAdmin($userinfo)) {
    $response = [
        "code" => "C",
        "data" => $themes
    ];
}
else {
    $response = [
        "code" => "E",
        "error" => L::needToLogin
    ];
}
sendJSON($response);
?>