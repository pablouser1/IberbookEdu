<?php
// Change cookie with new school and group
require_once("headers.php");
require_once("functions.php");
require_once("auth.php");
require_once("helpers/db.php");

$db = new DB;
$auth = new Auth;
if ($userinfo = $auth->isUserLoggedin()) {
    foreach ($userinfo["schools"] as $tempschool) {
        if ($tempschool->id == $_POST["schoolid"]) {
            $school = $tempschool;
            break;
        }
    }
    if (!isset($school)) {
        $response = [
            "code" => "E",
            "error" => "No se ha encontrado el centro que solicitaste"
        ];
        sendJSON($response);
    }
    foreach ($school->groups as $tempgroup) {
        if ($tempgroup->name == $_POST["group"]) {
            $group = $tempgroup;
            break;
        }
    }
    if (!isset($group)) {
        $response = [
            "code" => "E",
            "error" => "No se ha encontrado el grupo que solicitaste"
        ];
        sendJSON($response);
    }

    // Check if are not the same
    if ($userinfo["schoolid"] == $school->id && $userinfo["year"] == $group->name) {
        $response = [
            "code" => "E",
            "error" => "Ya utilizas esa combinación"
        ];
        sendJSON($response);
    }
    
    // Set new info
    $userinfo["schoolid"] = $school->id;
    $userinfo["schoolname"] = $school->name;
    $userinfo["year"] = $group->name;
    if ($userinfo["type"] == "teachers") {
        $userinfo["subject"] = $group->subject;
    }
    // Create user if doesn't exist and get ID
    $id = $auth->doesUserExists($userinfo);
    $userinfo["id"] = $id;
    $auth->setToken($userinfo);
    $response = [
        "code" => "C",
        "data" => $userinfo
    ];
    sendJSON($response);
}
?>
