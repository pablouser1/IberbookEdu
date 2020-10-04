<?php
if (!isset($_SESSION, $_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../helpers/db/db.php");
if (isset($_POST["id"])){
    if (isset($_POST["addschool"])){
        $stmt = $conn->prepare("INSERT INTO `schools` (`id`, `url`) VALUES (?, ?)");
        $stmt->bind_param("is", trim($_POST["id"]), trim($_POST["schoolurl"]));
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
    exit;
}
?>
