<?php

/**@var Leaf\App $app */

$app->group('/users', function() use ($app) {
    $app->get("/me", "UserController@me"); // Get me
    $app->post("/me/password", "UserController@password"); // Change password
    $app->post("/", "UserController@create"); // Create user(s)
    $app->delete("/(\d+)","UserController@delete"); // Delete user
});
