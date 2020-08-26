<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: login.php");
}
require_once("../helpers/db.php");
if (isset($_POST["id"])){
    if (isset($_POST["addschool"])){
        if(!isset($_POST["schoolname"])){
            die("No has escrito ningún nombre");
        }
        $stmt = $conn->prepare("INSERT INTO `schools` (`id`, `name`) VALUES (?, ?)");
        $stmt->bind_param("is", trim($_POST["id"]), trim($_POST["schoolname"]));
        if ($stmt->execute() !== true) {
            die("Error writing school info: " . $conn->error);
        }
    }
    elseif(isset($_POST["removeschool"])){
        $stmt = $conn->prepare("DELETE FROM `schools` WHERE id=?");
        $stmt->bind_param("s", trim($_POST["id"]));
        if ($stmt->execute() !== true) {
            die("Error deleting school info: " . $conn->error);
        }
    }
    header('Location: dashboard.php');
}
?>