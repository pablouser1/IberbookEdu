<?php
session_start();
// Send JSON to client
function sendJSON($response) {
    header('Content-type: application/json');
    echo (json_encode($response));
    exit;
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

// Add/remove vote
if (isset($_GET["id"])) {
    if ($userinfo["typeuser"] == "P"){
        $typeuser = "teachers";
    }
    else {
        $typeuser = "students";
    }


    $stmt = $conn->prepare("UPDATE $typeuser SET voted = ? WHERE id=? AND voted IS NULL");
    $stmt->bind_param("is", $_GET["id"], $userinfo["iduser"]);
    if ($stmt->execute() !== true){
        $response = [
            "code" => "E",
            "description" => "por favor, inténtelo de nuevo más tarde"
        ];
        sendJSON($response);
    }

    elseif ($stmt->affected_rows == 0) {
        // TODO Check if yearbook voted was removed

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
    if ($stmt->execute() !== true) {
        $response = [
            "code" => "E",
            "description" => "por favor, inténtelo de nuevo más tarde"
        ];
        sendJSON($response);
    }
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
