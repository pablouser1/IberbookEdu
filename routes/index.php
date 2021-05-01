<?php

use Helpers\Auth;

/*
|--------------------------------------------------------------------------
| Set up 404 handler
|--------------------------------------------------------------------------
|
| Create a handler for 404 errors. Uncomment the lines below to
| add a custom error 404 handler.
|
*/
// $app->set404(function() {
//     echo "error 404";
// });

/*
|--------------------------------------------------------------------------
| Set up Controller namespace
|--------------------------------------------------------------------------
|
| This allows you to directly use controller names instead of typing
| the controller namespace first.
|
*/
$app->setNamespace("\Controllers");

$app->get("/", function () {
    render("index", [
        "name" => getenv("INSTANCE_NAME"),
        "description" => getenv("INSTANCE_DESCRIPTION")
    ]);
});

$app->get("/instance", function () {
    $user = Auth::isUserLoggedin(false);
    $profile = Auth::isProfileLoggedin(false);
    if ($user) {
        $user = true;
    }
    if ($profile) {
        $profile = true;
    }
    return response([
        "name" => getenv("INSTANCE_NAME"),
        "description" => getenv("INSTANCE_DESCRIPTION"),
        "version" => Leaf\Config::get('app.version'),
        "user" => $user,
        "profile" => $profile
    ]);
});

// You can break up routes into modules
require __DIR__ . "/auth.php";
require __DIR__ . "/gallery.php";
require __DIR__ . "/groups.php";
require __DIR__ . "/profiles.php";
require __DIR__ . "/schools.php";
require __DIR__ . "/themes.php";
require __DIR__ . "/users.php";
require __DIR__ . "/yearbooks.php";
require __DIR__ . "/staff.php";
