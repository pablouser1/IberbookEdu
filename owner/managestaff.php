<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: login.php");
}
require_once("../helpers/db.php");
if (isset($_POST["username"])){
    if (isset($_POST["addstaff"])){
        $stmt = $conn->prepare("INSERT INTO `staff` (`username`, `password`, `permissions`) VALUES (?, ?, ?)");
        if ($_POST["addstaff"] == "admin"){
            $staff_password = null;
        }
        elseif($_POST["addstaff"] == "owner"){
            $staff_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        }
        $stmt->bind_param("sss", $_POST["username"], $staff_password, $_POST["addstaff"]);
        if ($stmt->execute() !== true) {
            die("Error writing staff info: " . $conn->error);
        }
    }
    elseif(isset($_POST["removestaff"])){
        if ($_POST["removestaff"] == "admin"){
            $staff_password = null;
        }
        elseif($_POST["removestaff"] == "owner"){
            $staff_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        }
        $stmt = $conn->prepare("DELETE FROM `staff` WHERE username=?");
        $stmt->bind_param("s", $_POST["username"]);
        if ($stmt->execute() !== true) {
            die("Error deleting staff info: " . $conn->error);
        }
    }
    header('Location: dashboard.php');
}
?>