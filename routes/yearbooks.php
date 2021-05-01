<?php

/**@var Leaf\App $app */

$app->group('/yearbooks', function() use ($app) {
    $app->get("/", "YearbookController@all");
    $app->get("/(\d+)", "YearbookController@one");
    $app->get("/random", "YearbookController@random");
    $app->get("/me", "YearbookController@me"); // Get logged profile's yearbook
    $app->post("/me", "YearbookController@generate"); // Generate yearbook
    $app->delete("/(\d+)", "YearbookController@delete"); // Delete yearbook
    $app->post("/(\d+)/vote", "YearbookController@vote"); // Vote
});
