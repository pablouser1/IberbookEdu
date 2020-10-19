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

// Check if user sends data
if (!isset($_POST["id"], $_POST["type"])) {
    $response = [
        "code" => "E",
        "description" => "Falta información"
    ];
    sendJSON($response);
}

$id = (int)$_POST["id"];

switch($_POST["type"]) {
    case "students":
    case "teachers":
        $typeuser = $_POST["type"];
    break;
    default:
    $response = [
        "code" => "E",
        "description" => "Ese tipo de usuario no existe"
    ];
    sendJSON($response);
}

// User is not admin
if ($_SESSION["loggedin"] !== "admin") {
    if ( ($id !== $userinfo["id"]) || ($typeuser !== $userinfo["typeuser"]) ) {
        $response = [
            "code" => "E",
            "description" => "Usuario incorrecto"
        ];
        sendJSON($response);
    }
}

// User is admin
else {
    if (isset($_POST["reason"])) {
        $reason = htmlspecialchars($_POST["reason"]);
    }
    else {
        $reason = null;
    }
}

if (isset($_POST["items"])) {
    // items into array
    $items = explode(",", $_POST["items"]);
    foreach ($items as $item) {
        if ($item == "video" || $item == "photo") {
            $stmt = $db->prepare("SELECT $item FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
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
                    unlink($uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/{$typeuser}/{$id}/{$filename}");
                }
            }
            $stmt->close();
        }
        // Base de datos
        // POSIBLE SQL INJECTION
        $stmt = $db->prepare("UPDATE users SET $item = NULL WHERE id=?");
        $stmt->bind_param("s", $id);
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
        $stmt = $db->prepare("UPDATE users SET `reason` = ? WHERE id=?");
        $stmt->bind_param("ss", $reason, $id);
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
        $stmt = $db->prepare("UPDATE users SET `reason` = NULL WHERE id=?");
        $stmt->bind_param("s", $id);
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
