<?php
require_once("config/config.php");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Access-Control-Allow-Credentials: true");

if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    
    if (in_array($origin, $frontends)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }
}


if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
};
?>
