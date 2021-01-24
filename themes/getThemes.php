<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../classes/themes.php");

$themesClass = new Themes;
$themes = $themesClass->getThemes();

$response = [
    "code" => "C",
    "data" => $themes
];
Utils::sendJSON($response);
?>
