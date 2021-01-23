<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../../helpers/db.php");
$db = new DB;
if (isset($_POST["schoolid"], $_GET["action"])){
    // ID and URL
    $id = trim($_POST["schoolid"]);
    switch ($_GET["action"]) {
        case "add":
            if (isset($_POST["schoolname"])) {
                $name = trim($_POST["schoolname"]);
            }
            else {
                die("Type a school name");
            }
            $stmt = $db->prepare("INSERT INTO `schools` (`id`, `name`) VALUES (?, ?)");
            $stmt->bind_param("is", $id, $name);
            if ($stmt->execute() !== true) {
                die("Error writing school info");
            }
            break;
        case "remove":
            $stmt = $db->prepare("DELETE FROM `schools` WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute() !== true) {
                die("Error deleting school");
            }
            break;
        default:
            die("Not a valid action");
    }
    header('Location: dashboard.php');
    exit;
}
else {
    die("Type a school ID");
}
?>
