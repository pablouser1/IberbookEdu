<?php
if (!isset($_SESSION, $_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../helpers/db/db.php");
$db = new DB;
if (isset($_POST["id"])){
    if (isset($_POST["addschool"])){
        $stmt = $db->prepare("INSERT INTO `schools` (`id`, `url`) VALUES (?, ?)");
        $stmt->bind_param("is", trim($_POST["id"]), trim($_POST["schoolurl"]));
        if ($stmt->execute() !== true) {
            die("Error al escribir los datos del centro");
        }
    }
    elseif(isset($_POST["removeschool"])){
        $stmt = $db->prepare("DELETE FROM `schools` WHERE id=?");
        $stmt->bind_param("s", trim($_POST["id"]));
        if ($stmt->execute() !== true) {
            die("Error al borrar los datos del centro");
        }
    }
    header('Location: dashboard.php');
    exit;
}
?>
