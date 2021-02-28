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

$tables = ["users", "profiles", "gallery", "messages"];
foreach ($tables as $table) {
    if ($db->query("DELETE FROM $table") === false) {
        die("Error while deleting {$table}");
    }
}

// Delete uploads folder
Utils::recursiveDelete($uploadpath);

header('Location: dashboard.php');
exit;
?>
