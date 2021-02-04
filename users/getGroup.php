<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../classes/groups.php");
$auth = new Auth;
$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo) {
    $groups = new Groups;
    if ($auth->isUserAdmin($userinfo)) {
        $users = $groups->getProfilesGroupFull($profileinfo["schoolid"], $profileinfo["year"]);
    }
    else {
        $users = $groups->getProfilesGroupBasic($profileinfo["schoolid"], $profileinfo["year"]);
    }
    $response = [
        "code" => "C",
        "data" => $users
    ];
    Utils::sendJSON($response);
}
?>
