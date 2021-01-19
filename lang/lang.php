<?php
// Load Composer's autoloader
require_once(__DIR__.'/../vendor/autoload.php');
$i18n = new i18n();
$i18n->setFilePath(__DIR__.'/../lang/lang_{LANGUAGE}.ini'); // language file path
$i18n->setCachePath(__DIR__.'/cache');
$i18n->setPrefix('L');
$i18n->setMergeFallback(true); // make keys available from the fallback language
// Set forced language
/*
if (isset($_GET["lang"])) {
    $i18n->setForcedLang($_GET["lang"]);
}
*/
$i18n->init();
?>
