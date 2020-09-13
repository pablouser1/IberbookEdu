<?php
session_start();
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== "admin") {
    header("Location: ../login.php");
    exit;
}
require_once("../helpers/db/db.php");
require_once("../helpers/config.php");

$userinfo = $_SESSION["userinfo"];
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

if ($_GET["type"] == ("students" || "teachers)")) {
    $type = $_GET["type"];
}
else {
    return "typenotvalid";
}
$stmt = $conn->prepare("DELETE FROM $type where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
if ($stmt->execute() !== true) {
    die("Error updating record: " . $conn->error);
}
$stmt->close();
recursiveRemoveDirectory($uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/$type/".$_GET["id"]);

header("Location: dashboard.php");
exit;
?>
