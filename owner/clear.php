<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: login.php");
    exit;
}
require_once("../helpers/db.php");
require_once("../helpers/config.php");

function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
}

function cleardb($tables) {
    foreach ($tables as $table) {
        $stmt = $conn->prepare("DELETE FROM $dir WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error deleting data: " . $conn->error);
        }
        $stmt->close();
    }
}
if (isset($_POST["id"], $_POST["clear"])){
    // Check first if id exists
    $stmt = $conn->prepare("SELECT id FROM schools WHERE id=?");
    $stmt->bind_param("i", $_POST["id"]);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        die("Ha habido un error al procesar tu solicitud, ¿has escrito bien el código?");
    }
    $stmt->close();

    $tables = ["students", "teachers", "gallery"];
    // Clear tables from database
    cleardb($tables);
    // Delete uploads folder
    recursiveRemoveDirectory($uploadpath.$_POST["id"]);
        
    header('Location: dashboard.php');
    exit;
}
?>

