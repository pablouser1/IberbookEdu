<?php
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header("Access-Control-Allow-Credentials: true");

if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];
    $allowed_domains = [
        "http://localhost:8080",
        "https://iberbookedu.onrender.com"
    ];
    
    if (in_array($origin, $allowed_domains)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }
}


if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
};
?>
