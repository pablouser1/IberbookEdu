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
        // Create user
        if ($type == "teachers") {
            $stmt = $db->prepare("INSERT INTO `teachers` (`userid`, `fullname`, `schoolid`, `schoolyear`, `subject`) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $userinfo["iduser"], $userinfo["nameuser"], $userinfo["idcentro"], $userinfo["yearuser"], $userinfo["subject"]);
        }
        else {
            $stmt = $db->prepare("INSERT INTO `students` (`userid`, `fullname`, `schoolid`, `schoolyear`) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $userinfo["iduser"], $userinfo["nameuser"], $userinfo["idcentro"], $userinfo["yearuser"]);
        }
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
    switch ($type) {
        case "teachers":
            $stmt = $db->prepare("SELECT id FROM $type WHERE userid=? AND schoolyear=? AND schoolid=?");
        break;
        case "students":
            $stmt = $db->prepare("SELECT id FROM $type WHERE userid=? AND schoolyear=? AND schoolid=?");
        break;
        default:
        die("Ese tipo de usuario no existe");
    }
    // Check if exists
    $stmt->bind_param("ssi", $userinfo["iduser"], $userinfo["yearuser"], $userinfo["idcentro"]);
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
