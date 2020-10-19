<?php
// TODO, delete user votes when yearbook get deleted
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("location: ../login.php");
}

function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
}

// Send json
function sendJSON($response) {
    header('Content-type: application/json');
    echo json_encode($response);
    exit;
}

require_once("../helpers/db/db.php");
require_once("../helpers/config.php");

$userinfo = $_SESSION["userinfo"];
$db = new DB;
// Year without spaces
$yearuser = str_replace(' ', '', $userinfo["yearuser"]);

if (isset($_GET["action"])) {
    // Get academic year (2020-2021 for example)
    $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
    switch ($_GET["action"]) {
        case "delete":
            // Delete yearbook
            $stmt = $db->prepare("DELETE FROM yearbooks WHERE schoolid=? and schoolyear=? and acyear=?");
            $stmt->bind_param("iss", $userinfo["idcentro"], $userinfo["yearuser"], $acyear);
            if ($stmt->execute() !== true) {
                $response = [
                    "code" => "E",
                    "description" => "Error al eliminar yearbook"
                ];
                sendJSON($response);
            }
            $stmt->close();
            recursiveRemoveDirectory($_SERVER["DOCUMENT_ROOT"].$ybpath.$userinfo["idcentro"]."/$acyear/".$yearuser);
        break;
        default:
        $response = [
            "code" => "E",
            "description" => "Opción no válida"
        ];
        sendJSON($response);
    }
}
else {
    $response = [
        "code" => "E",
        "description" => "No has mandado ninguna acción"
    ];
    sendJSON($response);
}

$response = [
    "code" => "C",
    "description" => "El yearbook se ha eliminado con éxito"
];
sendJSON($response);
?>
