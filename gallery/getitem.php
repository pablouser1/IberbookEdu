<?php
// Get item of gallery
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../config/config.php");
require_once("../helpers/db.php");

$db = new DB;
$auth = new Auth;
if ($userinfo = $auth->isUserLoggedin()) {
    $stmt = $db->prepare("SELECT name, id FROM gallery where id=? and schoolid=? and schoolyear=?");
    $stmt->bind_param("sis", $_GET["id"], $userinfo["schoolid"], $userinfo["year"]);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($medianame, $mediaid);
    $stmt->fetch();
    if ($stmt->num_rows == 1) {
        $filepath = $uploadpath.$userinfo["schoolid"]."/".$userinfo["year"]."/gallery/".$medianame;
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
}

?>
