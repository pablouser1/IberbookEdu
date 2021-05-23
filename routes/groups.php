<?php

/**@var Leaf\App $app */

$app->group('/groups', function() use ($app) {
    $app->get("/", "GroupController@all");
    $app->get("/me/members", "GroupController@members");
    $app->get("/(\d+)", "GroupController@one");
    $app->post("/(\d+)/yearbook", "YearbookController@generate"); // Generate yearbook of group
    $app->post("/", "GroupController@create"); // Create new group
    $app->post("/delete", "GroupController@delete"); // Delete group(s)
});
