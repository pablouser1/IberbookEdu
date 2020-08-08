<?php
// Gets yearbook
session_start();
require_once("helpers/db.php");
$userinfo = $_SESSION["userinfo"];

if (!isset($_SESSION["loggedin"])){
    header("Location: login.php");
}
$stmt = $conn->prepare("SELECT zipname, available FROM yearbooks WHERE schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($filename, $available);
$stmt->fetch();
if ($stmt->num_rows == 1) {
    if($_SESSION["loggedin"] == "admin" || $available == "user"){
        $filepath = "yearbooks/".$userinfo["idcentro"]."/".$userinfo["yearuser"]."/generated/";
        die(header("Location: $filepath/$filename"));
    }
    else{
        $yearbook_error = "No tienes permisos para descargar el yearbook";
    }
}
else{
    $yearbook_error = "No hay ningún yearbook disponible";
}
echo($yearbook_error);
?>