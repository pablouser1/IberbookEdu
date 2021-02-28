<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../classes/messages.php");
$auth = new Auth;
$userinfo = $auth->isUserLoggedin();
if ($userinfo && $auth->isUserAdmin($userinfo)) {
    $messagesClass = new Messages;
    if (isset($_POST["message"], $_POST["to"])) {
        $message = nl2br(htmlspecialchars($_POST["message"]));
        $response = [
            "code" => "E",
            "error" => "TODO"
        ];
    }
    else {
        $response = [
            "code" => "E",
            "error" => "Data not sent"
        ];
    }
    Utils::sendJSON($response);
}
?>
