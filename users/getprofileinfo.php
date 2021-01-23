<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");
require_once("../lang/lang.php");

$auth = new Auth;
$db = new DB;

if ($profileinfo = $auth->isProfileLoggedin()) {
    $stmt = $db->prepare("SELECT id, photo, video, link, quote, uploaded FROM profiles WHERE `id`=?");
    $stmt->bind_param("i", $profileinfo["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $response = [
            "code" => "C",
            "data" => $user
        ];
    }
    else {
        $response = [
            "code" => "E",
            "error" => L::user_notExist
        ];
    }
    sendJSON($response);
}
?>
