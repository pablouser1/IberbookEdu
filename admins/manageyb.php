<?php
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("location: ../login.php");
}
require_once("../helpers/db.php");
require_once("../helpers/config.php");

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
    // Get zip name
    $stmt = $conn->prepare("SELECT zipname FROM yearbooks WHERE schoolid=? and schoolyear=?");
    $stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows === 1) {
        $stmt->bind_result($zipname);
        $stmt->fetch();
    }
    else {
        die("No existe ese yearbook");
    }
    // Delete yearbook
    $stmt = $conn->prepare("DELETE FROM yearbooks WHERE schoolid=? and schoolyear=?");
    $stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
    if ($stmt->execute() !== true) {
        die("Error updating record: " . $conn->error);
    }
    $stmt->close();
    unlink($ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/".$zipname);
}

header("Location: dashboard.php");
?>
