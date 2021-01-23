<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}
require_once("../../functions.php");
require_once("../../helpers/db.php");
$db = new DB;
if (isset($_GET["action"])){
    switch ($_GET["action"]) {
        case "add":
            if (isset($_POST["groupname"])) {
                $name = trim($_POST["groupname"]);
            }
            else {
                die("Type a group name");
            }
            $stmt = $db->prepare("INSERT INTO `groups` (`name`) VALUES (?)");
            $stmt->bind_param("s", $name);
            if ($stmt->execute() !== true) {
                die("Error writing group info");
            }
            break;
        case "remove":
            if (isset($_POST["groupid"])) {
                $id = trim($_POST["groupid"]);
            }
            else {
                die("Choose a group");
            }
            $stmt = $db->prepare("DELETE FROM `groups` WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute() !== true) {
                die("Error deleting group");
            }
            break;
        default:
            die("Not a valid action");
    }
    header('Location: dashboard.php');
    exit;
}
else {
    die("Choose an action first");
}
?>
