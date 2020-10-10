<?php
// Handle user data (dashboard)
session_start();

if (!isset($_SESSION["loggedin"])) {
    header("Location: ../login.php");
    exit;
}

require_once("db/db.php");
require_once("config.php");

$db = new DB;

// Send json
function sendJSON($response) {
    header('Content-type: application/json');
    echo json_encode($response);
    exit;
}

$userinfo = $_SESSION["userinfo"];
$userid = $_POST["id"];
$typeuser = $_POST["type"];
if ($_SESSION["loggedin"] !== "admin") {
    if ( ($userid !== $userinfo["iduser"]) || ($typeuser !== $userinfo["typeuser"]) ) {
        $response = [
            "code" => "E",
            "description" => "Tipo de usuario incorrecto"
        ];
        sendJSON($response);
    }
}
else {
    if ($_POST["type"] == ("students" || "teachers")) {
        $reason = htmlspecialchars($_POST["reason"]);
    }
    else {
        $response = [
            "code" => "E",
            "description" => "Tipo de usuario incorrecto"
        ];
        sendJSON($response);
    }

}

if (isset($_POST["items"])) {
    // items into array
    $items = explode(",", $_POST["items"]);
    foreach ($items as $item) {
        if ($item == "video" || $item == "photo") {
            $stmt = $db->prepare("SELECT $item FROM $typeuser WHERE id=? AND schoolid=?");
            $stmt->bind_param("si", $userid, $userinfo["idcentro"]);
            if ($stmt->execute() !== true) {
                $response = [
                    "code" => "E",
                    "description" => "Ha habido un error la procesar tu solicitud"
                ];
                sendJSON($response);
            }
            $stmt->store_result();
            $stmt->bind_result($filename);
            if ($stmt->num_rows == 1) {
                if(($stmt->fetch()) == true) {
                    unlink($uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/{$typeuser}/{$userid}/{$filename}");
                }
            }
            $stmt->close();
        }
        // Base de datos
        $stmt = $db->prepare("UPDATE $typeuser SET ? = NULL WHERE id=?");
        $stmt->bind_param("ss", $item, $userid);
        if ($stmt->execute() !== true) {
            $response = [
                "code" => "E",
                "description" => "Ha habido un error la procesar tu solicitud"
            ];
            sendJSON($response);
        }
        $stmt->close();
    }
    if ($_SESSION["loggedin"] == "admin") {
        $stmt = $db->prepare("UPDATE $typeuser SET `reason` = ? WHERE id=?");
        $stmt->bind_param("ss", $reason, $userid);
        if ($stmt->execute() !== true) {
            $response = [
                "code" => "E",
                "description" => "Ha habido un error la procesar tu solicitud"
            ];
            sendJSON($response);
        }
        $stmt->close();
    }
    else {
        $stmt = $db->prepare("UPDATE $typeuser SET `reason` = NULL WHERE id=?");
        $stmt->bind_param("s", $userid);
        if ($stmt->execute() !== true) {
            $response = [
                "code" => "E",
                "description" => "Ha habido un error la procesar tu solicitud"
            ];
            sendJSON($response);
        }
        $stmt->close();
    }
    $response = [
        "code" => "C",
        "description" => null
    ];
    sendJSON($response);
}
else {
    $response = [
        "code" => "E",
        "description" => "No has elegido ninguna opción"
    ];
    sendJSON($response);
}
?>
