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
    if (isset($_POST["archive"])){
        // Teachers
        $stmt = $conn->prepare("DELETE FROM `teachers` WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error deleting teachers' info: " . $conn->error);
        }
        $stmt->close();
        // Students
        $stmt = $conn->prepare("DELETE FROM `students` WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error deleting students' info: " . $conn->error);
        }
        // Gallery
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM `gallery` WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error deleting gallery's info: " . $conn->error);
        }
        $stmt->close();
        // Yearbook
        $stmt = $conn->prepare("DELETE FROM `yearbooks` WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error deleting yearbook's info: " . $conn->error);
        }
        $stmt->close();

        // Delete uploads folder
        $dirs = array_filter(glob($ybpath.$_POST["id"]."/*"), 'is_dir');
        foreach ($dirs as $yearpath) {
            recursiveRemoveDirectory($yearpath."/uploads");
            // Also move zip to archive
            $zip = glob($yearpath."/*.zip")[0];
            if(!is_dir($yearpath."/archive")) {
                mkdir($yearpath."/archive", 0755);
            }
            rename($zip, $yearpath."/archive/".basename($zip));
        }
        
    }
    header('Location: dashboard.php');
    exit;
}
?>