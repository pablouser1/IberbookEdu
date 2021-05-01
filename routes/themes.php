<?php

/**@var Leaf\App $app */

$app->group('/themes', function() use ($app) {
    $app->get("/", "ThemeController@all");
    $app->get("/(\d+)", "ThemeController@one");
});
