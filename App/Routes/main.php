<?php
use App\Helpers\Auth;

use App\Middleware\CORS;

/*
|--------------------------------------------------------------------------
| Set up 404 handler
|--------------------------------------------------------------------------
|
| Create a handler for 404 errors
|
*/

$app->set404();

// MIDDLEWARE ON ALL PATHS
$app->add(new CORS);

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
        "name" => getenv("INSTANCE_NAME"),
        "description" => getenv("INSTANCE_DESCRIPTION")
    ]);
});

$app->get("/instance", function () {
    $loggedIn = false;
    $user = Auth::isUserLoggedin(false);
    if ($user) {
        $loggedIn = true;
    }
    return response([
        "name" => getenv("INSTANCE_NAME"),
        "description" => getenv("INSTANCE_DESCRIPTION"),
        "loggedin" => $loggedIn
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
$app->mount('/login', function() use ($app) {
    $app->post("/user", "AccountController@user");
    $app->post("/profile/(\d+)", "AccountController@profile");
});

Route("GET|POST", "/logout", "AccountController@logout");

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
|
| Manage users
|
*/
$app->mount('/users', function() use ($app) {
    $app->get("/me", "UserController@me"); // Get me
    $app->post("/me/password", "UserController@password"); // Change password
    $app->get("/(\d+)", "UserController@one"); // Get one
    $app->post("/", "UserController@create"); // Create user
});

/*
|--------------------------------------------------------------------------
| Profiles
|--------------------------------------------------------------------------
|
| Manage profiles
|
*/
$app->mount('/profiles', function() use ($app) {
    $app->post('/', "ProfileController@create");
    $app->get('/me', "ProfileController@me");
    $app->get("/me/current", "ProfileController@current"); // Get profile in use
    $app->get("/(\d+)", "ProfileController@one"); // Get one
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
$app->mount('/gallery', function() use ($app) {
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
$app->mount('/yearbooks', function() use ($app) {
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
$app->mount('/groups', function() use ($app) {
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
$app->mount('/schools', function() use ($app) {
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
$app->mount('/themes', function() use ($app) {
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
$app->mount('/staff', function() use ($app) {
    $app->post("/", "StaffController@create");
    $app->delete("/(\d+)", "StaffController@delete");
});
