<?php
use App\Helpers\Auth;

/*
|--------------------------------------------------------------------------
| Set up Controller namespace
|--------------------------------------------------------------------------
|
| This allows you to directly use controller names instead of typing
| the controller namespace first.
|
*/
$app->setNamespace("\App\Controllers");

include "./App/Routes/owner.php";
include "./App/Routes/setup.php";

$app->get("/", function () {
    render("index", [
        "name" => env("INSTANCE_NAME", "Default"),
        "description" => env("INSTANCE_DESCRIPTION", "An IberbookEdu Instance")
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
        "name" => env("INSTANCE_NAME", "Default"),
        "description" => env("INSTANCE_DESCRIPTION", "An IberbookEdu Instance"),
        "version" => Leaf\Config::get('app.version'),
        "user" => $user,
        "profile" => $profile
    ]);
});

/*
|--------------------------------------------------------------------------
| Account handler
|--------------------------------------------------------------------------
|
| Login, Logout
|
*/
$app->group('/login', function() use ($app) {
    $app->post("/user", "AccountController@user");
    $app->post("/profile/(\d+)", "AccountController@profile");
});

$app->post("/logout", "AccountController@logout");

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
|
| Manage users
|
*/
$app->group('/users', function() use ($app) {
    $app->get("/me", "UserController@me"); // Get me
    $app->post("/me/password", "UserController@password"); // Change password
    $app->post("/", "UserController@create"); // Create user(s)
    $app->delete("/(\d+)","UserController@delete"); // Delete user
});

/*
|--------------------------------------------------------------------------
| Profiles
|--------------------------------------------------------------------------
|
| Manage profiles
|
*/
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

/*
|--------------------------------------------------------------------------
| Gallery
|--------------------------------------------------------------------------
|
| Manage gallery
|
*/
$app->group('/gallery', function() use ($app) {
    $app->get("/", "GalleryController@all"); // Get all
    $app->post("/", "GalleryController@upload"); // Upload new
    $app->delete("/", "GalleryController@delete"); // Delete
    $app->get("/(\d+)", "GalleryController@one"); // Get one
});

/*
|--------------------------------------------------------------------------
| Yearbooks
|--------------------------------------------------------------------------
|
| Manage yearbooks
|
*/
$app->group('/yearbooks', function() use ($app) {
    $app->get("/", "YearbookController@all");
    $app->get("/(\d+)", "YearbookController@one");
    $app->get("/random", "YearbookController@random");
    $app->get("/me", "YearbookController@me"); // Get logged profile's yearbook
    $app->post("/me", "YearbookController@generate"); // Generate yearbook
    $app->delete("/(\d+)", "YearbookController@delete"); // Delete yearbook
    $app->post("/(\d+)/vote", "YearbookController@vote"); // Vote
});


/*
|--------------------------------------------------------------------------
| Groups
|--------------------------------------------------------------------------
|
| Manage groups
|
*/
$app->group('/groups', function() use ($app) {
    $app->get("/", "GroupController@all");
    $app->get("/me/members", "GroupController@members");
    $app->get("/(\d+)", "GroupController@one");
    $app->post("/", "GroupController@create"); // Create new group
    $app->delete("/(\d+)", "GroupController@delete");
});

/*
|--------------------------------------------------------------------------
| Schools
|--------------------------------------------------------------------------
|
| Manage schools
|
*/
$app->group('/schools', function() use ($app) {
    $app->get("/", "SchoolController@all");
    $app->get("/(\d+)", "SchoolController@one");
    $app->post("/", "SchoolController@create"); // Create new school
    $app->delete("/(\d+)", "SchoolController@delete");
});

/*
|--------------------------------------------------------------------------
| Themes
|--------------------------------------------------------------------------
|
| Manage themes
|
*/
$app->group('/themes', function() use ($app) {
    $app->get("/", "ThemeController@all");
    $app->get("/(\d+)", "ThemeController@one");
});

/*
|--------------------------------------------------------------------------
| Staff
|--------------------------------------------------------------------------
|
| Manage staff
|
*/
$app->group('/staff', function() use ($app) {
    $app->post("/", "StaffController@create");
    $app->delete("/(\d+)", "StaffController@delete");
});
