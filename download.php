<?php
// Gets yearbook
session_start();
require_once("helpers/db.php");
$userinfo = $_SESSION["userinfo"];

if (!isset($_SESSION["loggedin"])){
    header("Location: login.php");
}

$sql = "SELECT * FROM `yearbooks` WHERE `schoolid`='$userinfo[idcentro]' and `schoolyear`='$userinfo[yearuser]'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 1) {
    $filename = mysqli_fetch_row($result)[3];
    $available = mysqli_fetch_row($result)[5];
    if($_SESSION["loggedin"] !== "alumno" || $available == 1){
        $filepath = "yearbooks/".$userinfo["idcentro"]."/".$userinfo["yearuser"];
    }
    else{
        $yearbook_error = "No tienes permisos para descargar el yearbook";
    }
}
else{
    $yearbook_error = "No hay ningún yearbook disponible";
}
header("Location: $filepath/$filename");
?>