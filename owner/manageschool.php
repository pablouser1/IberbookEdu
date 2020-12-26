<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../helpers/db.php");
$db = new DB;
if (isset($_POST["id"])){
    // ID and URL
    $id = trim($_POST["id"]);
    $url = trim($_POST["schoolurl"]);
    if (isset($_POST["addschool"])){
        $stmt = $db->prepare("INSERT INTO `schools` (`id`, `url`) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $url);
        if ($stmt->execute() !== true) {
            die("Error al escribir los datos del centro");
        }
    }
    elseif(isset($_POST["removeschool"])){
        $stmt = $db->prepare("DELETE FROM `schools` WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute() !== true) {
            die("Error al borrar los datos del centro");
        }
    }
    header('Location: dashboard.php');
    exit;
}
else {
    die("Escribe el ID del centro");
}
?>
