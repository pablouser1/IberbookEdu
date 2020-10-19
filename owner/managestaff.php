<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../helpers/db/db.php");
$db = new DB;
if (isset($_POST["sendstaff"], $_POST["action"])) {
    switch ($_POST["sendstaff"]) {
        case "owner":
            if ($_POST["action"] == "add") {
                $stmt = $db->prepare("INSERT INTO `staff` (`username`, `password`, `permissions`) VALUES (?, ?, 'owner')");
            }
            elseif ($_POST["action"] == "remove") {
                $stmt = $db->prepare("DELETE FROM `staff` WHERE username=? AND password=?");
            }
            foreach ($_POST["username"] as $id => $username) {
                $owner_password = password_hash($_POST["password"][$id], PASSWORD_DEFAULT);
                $stmt->bind_param("ss", $username, $owner_password);
                if ($stmt->execute() !== true) {
                    die("Error al agregar/eliminar dueño");
                }
            }
            $stmt->close();
        break;
        case "admin":
            if ($_POST["action"] == "add") {
                $stmt = $db->prepare("INSERT INTO `staff` (`username`, `permissions`) VALUES (?, 'admin')");
            }
            elseif ($_POST["action"] == "remove") {
                $stmt = $db->prepare("DELETE FROM `staff` WHERE username=?");
            }

            foreach ($_POST["username"] as $username) {
                $stmt->bind_param("s", $username);
                if ($stmt->execute() !== true) {
                    die("Error al agregar/eliminar administrador");
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
