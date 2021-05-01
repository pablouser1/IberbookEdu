<?php

/**@var Leaf\App $app */

$app->group('/profiles', function() use ($app) {
    $app->get('/me', "ProfileController@me"); // Get all profiles of current user
    $app->get("/me/current", "ProfileController@current"); // Get profile in use
    $app->get("/(\d+)/photo", "ProfileController@photo"); // Get photo stream
    $app->get("/(\d+)/video", "ProfileController@video"); // Get video stream
    $app->post("/me/photo", "ProfileController@uploadMedia"); // Send photo
    $app->post("/me/video", "ProfileController@uploadMedia"); // Send video
    $app->post("/me/misc", "ProfileController@uploadMisc"); // Send quote and link
    $app->post("/(\d+)/items", "ProfileController@deleteItems"); // Delete items
});
