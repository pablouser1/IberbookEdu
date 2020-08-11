<?php
// Gets yearbook
session_start();
require_once("helpers/db.php");
require_once("helpers/config.php");
if (!isset($_SESSION["loggedin"])){
    header("Location: login.php");
}

$userinfo = $_SESSION["userinfo"];

$stmt = $conn->prepare("SELECT zipname, available FROM yearbooks WHERE schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($filename, $available);
$stmt->fetch();
if ($stmt->num_rows == 1) {
    if($_SESSION["loggedin"] == "admin" || $available == "1"){
        $filepath = $ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/generated/";
        $fullname = $filepath.$filename;
        // https://stackoverflow.com/a/27805443 and https://stackoverflow.com/a/23447332
        if(file_exists($fullname)){
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            header('Content-Type: ' . finfo_file($finfo, $fullname));
            finfo_close($finfo);
            header('Content-Disposition: attachment; filename='.$filename);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fullname));
            ob_clean();
            ob_end_flush();
            readfile($fullname);
        }
        else{
            $yearbook_error = "Ha habido un error al descargar el yearbook";
        }
    }
    else{
        $yearbook_error = "No tienes permisos para descargar el yearbook";
    }
}
else{
    $yearbook_error = "No hay ningún yearbook disponible";
}
if(isset($yearbook_error)) echo($yearbook_error);
?>
