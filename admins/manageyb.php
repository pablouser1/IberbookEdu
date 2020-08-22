<?php
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("location: ../login.php");
}
require_once("../helpers/db.php");
require_once("../helpers/config.php");

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
$userinfo = $_SESSION["userinfo"];

if (isset($_GET["makeavailable"]) && $_GET["makeavailable"] == "true"){
    // Make yearbook available to users
    $stmt = $conn->prepare("UPDATE yearbooks SET available=1 WHERE schoolid=? and schoolyear=?");
    $stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
    if ($stmt->execute() !== true) {
        die("Error updating record: " . $conn->error);
    }
    $stmt->close();
}

if (isset($_GET["deleteyearbook"]) && $_GET["deleteyearbook"] == "true"){
    $stmt = $conn->prepare("DELETE FROM yearbooks WHERE schoolid=? and schoolyear=?");
    $stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
    if ($stmt->execute() !== true) {
        die("Error updating record: " . $conn->error);
    }
    $stmt->close();
    delete_files($ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/generated");
}

header("Location: dashboard.php");
?>