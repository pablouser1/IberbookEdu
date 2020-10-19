<?php
if(!isset($_SESSION["loggedin"])){
    header("Location: ../login.php");
    exit;
}
require_once("db/db.php");
$db = new DB;

// Create profile
function createprofile($userinfo, $type) {
    // Get global vars
    global $db;
    $exists = check($userinfo, $type);
    // User already exists, continue
    if ($exists) {
        return $exists;
    }
    else {
        if (!isset($userinfo["subject"])) {
            $subject = null;
        }
        else {
            $subject = $userinfo["subject"];
        }
        // Create user
        $stmt = $db->prepare("INSERT INTO `users` (`userid`, `type`, `fullname`, `schoolid`, `schoolyear`, `subject`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $userinfo["iduser"], $type, $userinfo["nameuser"], $userinfo["idcentro"], $userinfo["yearuser"], $subject);
        if ($stmt->execute() !== true) {
            die("Error al crear usuario");
        }
        $stmt->close();
        // Get id generated
        $id = check($userinfo, $type);
        if ($id) return $id;
        else {
            die("Hubo un error al generar el usuario");
        }
    }
}
// Check if exists
function check($userinfo, $type) {
    // Get global vars
    global $db;
    $stmt = $db->prepare("SELECT id FROM users WHERE `type`=? AND userid=? AND schoolyear=? AND schoolid=?");
    // Check if exists
    $stmt->bind_param("sssi", $userinfo["typeuser"], $userinfo["iduser"], $userinfo["yearuser"], $userinfo["idcentro"]);
    $stmt->execute();
    $stmt->store_result();
    // Get profile id
    $stmt->bind_result($idprofile);
    $stmt->fetch();
    $exists = $stmt->num_rows;
    $stmt->close();
    if ($exists == 0) {
        return false;
    }
    else {
        return $idprofile;
    }
}
?>
