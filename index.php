<?php

use Middleware\CORS;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/
require_once __DIR__ . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Bring in (env)
|--------------------------------------------------------------------------
|
| Quickly use our environment variables
|
*/
\Dotenv\Dotenv::create(__DIR__)->load();

date_default_timezone_set(getenv("INSTANCE_TIMEZONE"));

/*
|--------------------------------------------------------------------------
| Register The Leaf Auto Loader
|--------------------------------------------------------------------------
|
| Require all Leaf API's Files
|
*/
require __DIR__ . "/Config/bootstrap.php";

/*
|--------------------------------------------------------------------------
| Initialise Shortcut Functions
|--------------------------------------------------------------------------
|
| Simple functions you can call from anywhere in your application.
| This is not a core feature, you can remove it and your app would still
| work fine.
|
*/
require __DIR__ . "/Config/functions.php";

/*
|--------------------------------------------------------------------------
| Initialise Leaf Core
|--------------------------------------------------------------------------
|
| Plant a seed, grow the stem and return Leaf🤷‍
|
*/
$app = new Leaf\App(AppConfig());

// CORS
$app->add(new CORS);

// Server down response
$app->setDown(function () {
    // Workaround, cors is not available here
    $cors = new CORS;
    $cors->call();
    throwErr("Server is in maintenance, please check back later", 503);
});

/*
|--------------------------------------------------------------------------
| Route Config
|--------------------------------------------------------------------------
|
| Require app routes.
|
*/
require __DIR__ . "/routes/index.php";

/*
|--------------------------------------------------------------------------
| Run Leaf Application
|--------------------------------------------------------------------------
|
| Require app routes
|
*/
$app->run();
