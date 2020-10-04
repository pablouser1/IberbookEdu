<?php
session_start();
// Send JSON to client
function sendJSON($response) {
    header('Content-type: application/json');
    echo (json_encode($response));
    exit;
}

// Send database error to client
function DBError() {
    $response = [
        "code" => "E",
        "description" => "por favor, inténtelo de nuevo más tarde"
    ];
    sendJSON($response);
}

if (!isset($_SESSION["loggedin"])){
    $response = [
        "code" => "E",
        "description" => "necesitas iniciar sesión para votar"
    ];
    sendJSON($response);
}

$userinfo = $_SESSION["userinfo"];
require_once("helpers/db/db.php");

// Add votes
if (isset($_GET["id"])) {
    $ybexist = false;

    if ($userinfo["typeuser"] == "P"){
        $typeuser = "teachers";
    }
    else {
        $typeuser = "students";
    }
    // Get yearbook info
    $stmt = $conn->prepare("SELECT schoolid, schoolyear FROM yearbooks WHERE id=?");
    $stmt->bind_param("i", $_GET["id"]);
    if ($stmt->execute() !== true) DBError();
    $result = $stmt->get_result();
    $value = $result->fetch_assoc();
    $stmt->close();
    // Check if user is trying to vote his own group
    if (!$value) {
        // Invalid id
        $response = [
            "code" => "E",
            "description" => "ese yearbook no existe"
        ];
        sendJSON($response);
    }
    elseif ( ($value["schoolid"] == $userinfo["idcentro"]) && ($value["schoolyear"] == $userinfo["yearuser"]) ) {
        $response = [
            "code" => "E",
            "description" => "no puedes votar a tu propio grupo"
        ];
        sendJSON($response);
    }
    // Set ybexists to true if yearbook exists
    if (!empty($value)) $ybexist = true;
    
    // Get user yearbook voted id
    $stmt = $conn->prepare("SELECT voted FROM $typeuser WHERE id=?");
    $stmt->bind_param("s", $userinfo["iduser"]);
    if ($stmt->execute() !== true) DBError();
    $result = $stmt->get_result();
    $ybinfo = $result->fetch_assoc();
    $uservote = !empty($ybinfo["voted"]) ? (int)$ybinfo["voted"] : null;
    $stmt->close();

    // Allow voting if user voted a yearbook that has been deleted
    if (!$ybexist) {
        $stmt = $conn->prepare("UPDATE $typeuser SET voted =? WHERE id=?");
    }
    else {
        $stmt = $conn->prepare("UPDATE $typeuser SET voted =? WHERE id=? AND voted IS NULL");
    }
    
    $stmt->bind_param("is", $_GET["id"], $userinfo["iduser"]);
    if ($stmt->execute() !== true) DBError();
    elseif ($stmt->affected_rows == 0) {
        $response = [
            "code" => "E",
            "description" => "ya has votado anteriormente"
        ];
        sendJSON($response);
    }
    $stmt->close();
    
    // Update yearbook data with one more vote
    $stmt = $conn->prepare("UPDATE yearbooks SET voted = voted + 1 WHERE id=?");
    $stmt->bind_param("i", $_GET["id"]);
    if ($stmt->execute() !== true) DBError();
    $stmt->close();
}
// If vars are not set send error
else {
    $response = [
        "code" => "E",
        "description" => "no has seleccionado ningún yearbook"
    ];
    sendJSON($response);
}

// Set response in json if everything went ok
$response = [
    "code" => "C",
    "description" => null
];
sendJSON($response);
?>
