<?php

/**@var Leaf\App $app */

$app->group('/gallery', function() use ($app) {
    $app->get("/(\d+)", "GalleryController@all"); // Get all items
    $app->post("/(\d+)", "GalleryController@upload"); // Upload new item
    $app->delete("/(\d+)", "GalleryController@delete"); // Delete gallery
    $app->get("/(\d+)/items/(\d+)", "GalleryController@one"); // Stream item
});
