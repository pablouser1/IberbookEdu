<?php
require_once(__DIR__."/../config/config.php");
// Check login system used
switch ($login) {
    case "ced":
        require_once("api/ced.php");
    break;
    case "local":
        require_once("api/local.php");
    break;
    default:
        die("That login method doesn't exist");
}
?>
