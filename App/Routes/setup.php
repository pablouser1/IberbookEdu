<?php
/*
|--------------------------------------------------------------------------
| SETUP
|--------------------------------------------------------------------------
|
| Endpoints used for initial setup
|
|
*/

use App\Models\Staff;
use App\Models\Theme;

$app->get("/setup", function() {
    if (file_exists(".setupDone")) {
        throwErr("Setup already done", 400);
    }
    render("setup");
});

$app->post("/setup", function() {
    if (file_exists(".setupDone")) {
        throwErr("Setup already done", 400);
    }
    $host = getenv("DB_HOST");
    $port = getenv("DB_PORT");
    $username = getenv("DB_USERNAME");
    $password = getenv("DB_PASSWORD");
    $name = getenv("DB_DATABASE");
    $db = new mysqli($host, $username, $password, $name, $port);
    $owner = $_POST["owner"];
    $db->query("CREATE TABLE users(
        id INT NOT NULL AUTO_INCREMENT,
        username VARCHAR(24) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `type` VARCHAR(12) NOT NULL,
        `role` VARCHAR(12) NOT NULL,
        `name` VARCHAR(128) NOT NULL,
        `surname` VARCHAR(128) NOT NULL,
        email VARCHAR(128),
        voted int,
        PRIMARY KEY(id)
    )");
    $db->query("CREATE TABLE profiles(
        id INT NOT NULL AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `group_id` INT NOT NULL,
        photo VARCHAR(255),
        video VARCHAR(255),
        link VARCHAR(255),
        quote VARCHAR(280),
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `subject` VARCHAR(24),
        PRIMARY KEY(id)
    )");

    $db->query("CREATE TABLE gallery(
        id INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) not null,
        `group_id` INT NOT NULL,
        `type` VARCHAR(8) NOT NULL,
        PRIMARY KEY(id)
    )");

    $db->query("CREATE TABLE `yearbooks` (
        `id` int NOT NULL AUTO_INCREMENT,
        `group_id` INT NOT NULL,
        `acyear` VARCHAR(16) NOT NULL,
        `banner` VARCHAR(30),
        `votes` INT DEFAULT '0',
        PRIMARY KEY(id)
    )");

    $db->query("CREATE TABLE `staff` (
        `id` int NOT NULL AUTO_INCREMENT,
        `username` varchar(14) NOT NULL UNIQUE,
        `password` varchar(80) NOT NULL,
        `role` varchar(12) NOT NULL,
        primary key(id)
    )");

    $db->query("CREATE TABLE `schools` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(128) NOT NULL,
        PRIMARY KEY(id)
    )");

    $db->query("CREATE TABLE `groups` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(32) NOT NULL,
        `school_id` INT NOT NULL,
        primary key(id)
    )");

    $db->query("CREATE TABLE `themes` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(32) NOT NULL UNIQUE,
        PRIMARY KEY(id)
    )");

    $owner_password = password_hash($owner["password"], PASSWORD_DEFAULT);
    $newStaff = new Staff;
    $newStaff->username = $owner["username"];
    $newStaff->password = $owner_password;
    $newStaff->role = "owner";
    $newStaff->save();

    $theme = new Theme;
    $theme->name = "default";
    $theme->save();
    file_put_contents(".setupDone", "created");
    response()->redirect("instance");
});
