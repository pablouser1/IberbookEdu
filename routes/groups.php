<?php

/**@var Leaf\App $app */

$app->group('/groups', function() use ($app) {
    $app->get("/", "GroupController@all");
    $app->get("/me/members", "GroupController@members");
    $app->get("/(\d+)", "GroupController@one");
    $app->post("/", "GroupController@create"); // Create new group
    $app->delete("/(\d+)", "GroupController@delete");
});
