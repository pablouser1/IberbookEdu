<?php
// Get pic and vid of user
session_start();
require_once("helpers/db.php");
require_once("helpers/config.php");
if (!isset($_SESSION["loggedin"])){
    header("Location: login.php");
}
$userinfo = $_SESSION["userinfo"];
switch($_GET["type"]){
    case "ALU":
        $type = "students";
    break;
    case "P":
        $type = "teachers";
    break;
    default:
        die("Ese tipo de usuario no existe");
}


if($_GET["media"] == "picname" || "vidname"){
    $stmt = $conn->prepare("SELECT $_GET[media], id FROM $type where id=?");
    $stmt->bind_param("s", $_GET["id"]);
}
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($medianame, $mediaid);
$stmt->fetch();
$downloadable = 0;
if ($stmt->num_rows == 1) {
    if ($_SESSION["loggedin"] == "admin"){
        $downloadable = 1;
    }
    elseif($_SESSION["loggedin"] == "user" && $mediaid == $userinfo["iduser"]){
        $downloadable = 1;
    }
    else{
        echo("No tienes permisos para descargar eso");
    }
}
else{
    echo("No se ha podido encontrar los datos que solicitaste");
}

if ($downloadable == 1){
    $filepath = $ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/uploads/".$type."/".$mediaid."/".$medianame;
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
?>
