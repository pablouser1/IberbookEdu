<?php
// Get pic of gallery
session_start();
require_once("helpers/db.php");
require_once("helpers/config.php");
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("Location: login.php");
}
$userinfo = $_SESSION["userinfo"];
$stmt = $conn->prepare("SELECT picname, id FROM gallery where id=? and schoolid=? and schoolyear=?");
$stmt->bind_param("sis", $_GET["id"], $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($medianame, $mediaid);
$stmt->fetch();
$downloadable = 0;
if ($stmt->num_rows == 1) {
    $filepath = $ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/uploads/"."gallery/".$medianame;
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
        ob_clean();
        ob_end_flush();
        readfile($filepath);
        exit();
    }
}
else{
    echo("No se ha podido encontrar los datos que solicitaste");
}
?>
