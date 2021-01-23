<?php
require_once("../../headers.php");
require_once("../../functions.php");
require_once("../../auth.php");
require_once("../../helpers/db.php");
require_once("../../config/config.php");

function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
}

$db = new DB;
$auth = new Auth;

$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo && $auth->isUserAdmin($userinfo)) {
    // Get academic year (2020-2021 for example)
    $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
    // Get yearbook id
    $stmt = $db->prepare("SELECT id FROM yearbooks WHERE schoolid=? AND schoolyear=? AND acyear=?");
    $stmt->bind_param("iss", $profileinfo["schoolid"], $profileinfo["year"], $acyear);
    $stmt->execute();
    $stmt->bind_result($ybid);
    $stmt->fetch();
    $stmt->close();

    // Delete yearbook
    $stmt = $db->prepare("DELETE FROM yearbooks WHERE schoolid=? and schoolyear=? and acyear=?");
    $stmt->bind_param("iss", $profileinfo["schoolid"], $profileinfo["year"], $acyear);
    $stmt->execute();
    $stmt->close();
    recursiveRemoveDirectory(__DIR__."/../../yearbooks/".$ybid);
    $response = [
        "code" => "C"
    ];
    sendJSON($response);
}
?>
