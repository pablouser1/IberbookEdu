<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}
require_once("../helpers/db.php");
require_once("../helpers/config.php");
$db = new DB;
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
        $stmt = $db->prepare("DELETE FROM $table WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error al intentar eliminar la base de datos");
        }
        $stmt->close();
    }
}

if (isset($_POST["id"], $_POST["clear"])){
    // Check first if id exists
    $stmt = $db->prepare("SELECT id FROM schools WHERE id=?");
    $stmt->bind_param("i", $_POST["id"]);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        die("Ha habido un error al procesar tu solicitud, ¿has escrito bien el código?");
    }
    $stmt->close();

    $tables = ["users", "gallery"];
    // Clear tables from database
    cleardb();
    // Delete uploads folder
    recursiveRemoveDirectory($uploadpath.$_POST["id"]);
        
    header('Location: dashboard.php');
    exit;
}
?>
