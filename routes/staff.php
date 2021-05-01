<?php

/**@var Leaf\App $app */

$app->group('/staff', function() use ($app) {
    $app->post("/", "StaffController@create");
    $app->delete("/(\d+)", "StaffController@delete");
});

$app->get("/staff/login", function () {
    $owner = Auth::isStaffLoggedin(false);
    if ($owner) {
        response()->redirect("owner/dashboard");
    }
    else {
        render("staff/login");
    }
});

$app->post("/staff/login", "AccountController@staff");

$app->get("/staff/owner/dashboard", function () {
    $user = Auth::isStaffLoggedin();

    // Staff
    $staff = Staff::all();

    // Schools
    $schools = School::all();

    // Groups
    $groups = Group::all();

    // Themes
    $themes = Theme::all();

    render("staff/owner/dashboard",
    [
        "user" => $user,
        "schools" => $schools,
        "staff" => $staff,
        "groups" => $groups,
        "themes" => $themes
    ]);
});

$app->get("/staff/owner/users", function () {
    $user = Auth::isStaffLoggedin();

    // Staff
    $users = User::all();
    $profiles = Profile::all();
    $groups = json_encode(Group::all());

    render("staff/owner/users",
    [
        "user" => $user,
        "users" => $users,
        "profiles" => $profiles,
        "groups" => $groups
    ]);
});
