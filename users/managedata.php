<?php
// Handle user data (dashboard)
session_start();

if (!isset($_SESSION["loggedin"])) {
    header("Location: ../login.php");
    exit;
}
require_once("../helpers/db.php");
require_once("../helpers/config.php");
function delete_files($dir) {
    $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
    $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($it as $file) {
        if ($file->isDir()) rmdir($file->getPathname());
        else unlink($file->getPathname());
    }
    rmdir($dir);
}

$userinfo = $_SESSION["userinfo"];

if ($userinfo["typeuser"] == "P"){
    $typeuser = "teachers";
}
elseif ($userinfo["typeuser"] == "ALU") {
    $typeuser = "students";
}

if (isset($_GET["action"], $typeuser)) {
    switch ($_GET["action"]) {
        case "delete":
            // Base de datos
            $stmt = $conn->prepare("DELETE FROM $typeuser WHERE id=?");
            $stmt->bind_param("s", $userinfo["iduser"]);
            if ($stmt->execute() !== true) {
                die("Error deleting data: " . $conn->error);
            }
            $stmt->close();
            // Ficheros
            delete_files($uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/$typeuser/".$userinfo["iduser"]);
        break;
    }
}
header("Location: dashboard.php");
?>
