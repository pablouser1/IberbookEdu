<?php
require_once(__DIR__."/../config/config.php");
// Check login system used
switch ($login) {
    case "local":
        require_once("api/local.php");
    break;
    default:
        die("That login method doesn't exist");
}
?>
