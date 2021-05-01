<?php

/**@var Leaf\App $app */

$app->group('/gallery', function() use ($app) {
    $app->get("/", "GalleryController@all"); // Get all
    $app->post("/", "GalleryController@upload"); // Upload new
    $app->delete("/", "GalleryController@delete"); // Delete
    $app->get("/(\d+)", "GalleryController@one"); // Get one
});
