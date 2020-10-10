<?php
// Get pic of gallery
session_start();
require_once("helpers/db/db.php");
require_once("helpers/config.php");
if (!isset($_SESSION["loggedin"])){
    header("Location: login.php");
    exit;
}
$userinfo = $_SESSION["userinfo"];
$db = new DB;
$stmt = $db->prepare("SELECT name, id FROM gallery where id=? and schoolid=? and schoolyear=?");
$stmt->bind_param("sis", $_GET["id"], $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($medianame, $mediaid);
$stmt->fetch();
if ($stmt->num_rows == 1) {
    $filepath = $uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/gallery/".$medianame;
    // https://stackoverflow.com/a/27805443 and https://stackoverflow.com/a/23447332
    if(file_exists($filepath)){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        header('Content-Type: ' . finfo_file($finfo, $filepath));
        finfo_close($finfo);
        header('Content-Disposition: inline; filename="'.basename($filepath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}
else{
    echo("No se ha podido encontrar la foto que solicitaste");
}
?>
