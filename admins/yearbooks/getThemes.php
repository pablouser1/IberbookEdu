<?php
require_once("../../headers.php");
require_once("../../functions.php");
require_once("../../auth.php");
require_once("themes.php");

$response = [
    "code" => "C",
    "data" => $themes
];
sendJSON($response);
?>