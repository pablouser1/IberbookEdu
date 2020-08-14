<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: login.php");
}
require_once("../helpers/db.php");
if (isset($_POST["id"])){
    if (isset($_POST["cleardb"])){
        // Teachers
        $stmt = $conn->prepare("DELETE FROM `teachers` WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error deleting teachers' info: " . $conn->error);
        }
        $stmt->close();
        // Students
        $stmt = $conn->prepare("DELETE FROM `students` WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error deleting students' info: " . $conn->error);
        }
        // Gallery
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM `gallery` WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error deleting gallery's info: " . $conn->error);
        }
        $stmt->close();
        // Yearbook
        $stmt = $conn->prepare("DELETE FROM `yearbooks` WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error deleting yearbook's info: " . $conn->error);
        }
        $stmt->close();
    }
    header('Location: dashboard.php');
}
?>