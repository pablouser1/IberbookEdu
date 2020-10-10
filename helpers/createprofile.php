<?php
if(!isset($_SESSION["loggedin"])){
    header("Location: ../login.php");
    exit;
}
require_once("db/db.php");
$userinfo = $_SESSION["userinfo"];
$db = new DB;

switch ($userinfo["typeuser"]) {
    case "ALU":
        $typeuser = "students";
    break;
    case "P":
        $typeuser = "teachers";
    break;
    default:
        die("Error al crear cuenta, usuario inválido");
}

// Check if exists

$stmt = $db->prepare("SELECT id FROM $typeuser WHERE id=? AND schoolyear=?");
$stmt->bind_param("ss", $userinfo["iduser"], $userinfo["yearuser"]);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) {
    $stmt->close();
    // Create user
    if ($typeuser == "teachers") {
        $stmt = $db->prepare("INSERT INTO `teachers` (`id`, `fullname`, `schoolid`, `schoolyear`, `subject`) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $userinfo["iduser"], $userinfo["nameuser"], $userinfo["idcentro"], $userinfo["yearuser"], $userinfo["subject"]);
    }
    else {
        $stmt = $db->prepare("INSERT INTO 'students' (id, fullname, schoolid, schoolyear) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $userinfo["iduser"], $userinfo["nameuser"], $userinfo["idcentro"], $userinfo["yearuser"]);
    }

    if ($stmt->execute() !== true) {
        die("Error al crear usuario");
    }
    $stmt->close();
}

?>
