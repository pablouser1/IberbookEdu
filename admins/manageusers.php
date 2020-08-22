<?php
session_start();
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== "admin") {
    header("Location: ../login.php");
}
require_once("../helpers/db.php");
require_once("../helpers/config.php");

$userinfo = $_SESSION["userinfo"];
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

        foreach($files as $file){
            delete_files($file);
        }
    
        rmdir($target);
    } elseif(is_file($target)) {
        unlink($target);  
    }
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
delete_files($ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/uploads/students/".$_GET["id"]);

header("Location: dashboard.php");
?>
