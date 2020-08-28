<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: login.php");
    exit;
}
require_once("../helpers/db.php");
if (isset($_POST["sendstaff"], $_POST["action"])) {
    switch ($_POST["sendstaff"]) {
        case "owner":
            if ($_POST["action"] == "add") {
                $stmt = $conn->prepare("INSERT INTO `staff` (`username`, `password`, `permissions`) VALUES (?, ?, 'owner')");
            }
            elseif ($_POST["action"] == "remove") {
                $stmt = $conn->prepare("DELETE FROM `staff` WHERE username=? AND password=?");
            }
            foreach ($_POST["username"] as $id => $usernname) {
                $owner_password = password_hash($_POST["password"][$id], PASSWORD_DEFAULT);
                $stmt->bind_param("ss", $usernname, $owner_password);
                if ($stmt->execute() !== true) {
                    die("Error executing command: " . $conn->error);
                }
            }
            $stmt->close();
        break;
        case "admin":
            if ($_POST["action"] == "add") {
                $stmt = $conn->prepare("INSERT INTO `staff` (`username`, `permissions`) VALUES (?, 'admin')");
            }
            elseif ($_POST["action"] == "remove") {
                $stmt = $conn->prepare("DELETE FROM `staff` WHERE username=?");
            }

            foreach ($_POST["username"] as $usernname) {
                $stmt->bind_param("s", $usernname);
                if ($stmt->execute() !== true) {
                    die("Error executing command: " . $conn->error);
                }
            }
            $stmt->close();
        break;
        default:
        die("Sólo puedes agregar o eliminar admins o dueños");
    }
    header('Location: dashboard.php');
    exit;
}
?>
