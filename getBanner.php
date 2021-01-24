<?php
// Gets random banner, used in home page
require_once("functions.php");
require_once("headers.php");
require_once("classes/yearbooks.php");

$yearbooks = new Yearbooks;

$random = $yearbooks->getRandom();
if ($random) {
    $response = [
        "code" => "C",
        "data" => $random
    ];
}
else {
    $response = [
        "code" => "E"
    ];
}
Utils::sendJSON($response);
?>
