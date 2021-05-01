<?php

/**@var Leaf\App $app */

$app->group('/login', function() use ($app) {
    $app->post("/user", "AccountController@user");
    $app->post("/profile/(\d+)", "AccountController@profile");
});

$app->post("/logout", "AccountController@logout");
