<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../../functions.php");
require_once("../../helpers/db.php");
require_once("../../config/config.php");

$db = new DB;

function cleardb($tables) {
    foreach ($tables as $table) {
        $stmt = $db->prepare("DELETE FROM $table WHERE schoolid=?");
        $stmt->bind_param("i", $_POST["id"]);
        if ($stmt->execute() !== true) {
            die("Error while truncating database");
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
        die("There was an error processing your request, did you type the id correctly?");
    }
    $stmt->close();

    $tables = ["users", "profiles", "gallery"];
    // Clear tables from database
    cleardb($tables);
    // Delete uploads folder
    Utils::recursiveDelete($uploadpath."/".$_POST["id"]);
        
    header('Location: dashboard.php');
    exit;
}
?>
