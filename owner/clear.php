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

if (isset($_POST["id"])){
    if (isset($_POST["clear"])){
        // Delete uploads folder
        recursiveRemoveDirectory($uploadpath.$_POST["id"]);
        
    }
    header('Location: dashboard.php');
    exit;
}
?>
