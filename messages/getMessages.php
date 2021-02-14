<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../classes/messages.php");
$auth = new Auth;
$userinfo = $auth->isUserLoggedin();
if ($userinfo) {
    $messagesClass = new Messages;
    if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
        $offset = $_GET["offset"];
    }
    else {
        $offset = 0;
    }
    $messages = $messagesClass->getMessages($userinfo["id"], $offset);
    if ($messages) {
        $response = [
            "code" => "C",
            "data" => $messages
        ];
    }
    else {
        $response = [
            "code" => "NO-MORE"
        ];
    }
}
else {
    http_response_code(401);
    $response = [
        "code" => "E",
        "error" => "Not logged in"
    ];
}
Utils::sendJSON($response);
?>
