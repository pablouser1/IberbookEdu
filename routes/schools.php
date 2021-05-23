<?php

/**@var Leaf\App $app */

$app->group('/schools', function() use ($app) {
    $app->get("/", "SchoolController@all");
    $app->get("/(\d+)", "SchoolController@one");
    $app->post("/", "SchoolController@create"); // Create new school
    $app->post("/delete", "SchoolController@delete"); // Delete school(s)
});
