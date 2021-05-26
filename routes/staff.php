<?php

use Helpers\Auth;
use Helpers\Misc;
use Models\Gallery;
use Models\Group;
use Models\Profile;
use Models\School;
use Models\Staff;
use Models\Theme;
use Models\User;

/**@var Leaf\App $app */

$app->group('/staff', function() use ($app) {
    $app->post("/", "StaffController@create"); // Create staff(s)
    $app->post("/delete", "StaffController@delete"); // Delete staff(s)
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

$app->group("/staff/owner", function () use($app) {
    $app->get("/dashboard", function () {
        $user = Auth::isStaffLoggedin("owner");

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

    $app->get("/users", function () {
        $user = Auth::isStaffLoggedin("owner");

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
    $app->get("/log", function() {
        $owner = Auth::isStaffLoggedin("owner");
        $log = \Leaf\Config::get("log.dir") . \Leaf\Config::get("log.file");
        if (is_file($log)) {
            response()->header("Content-type", "text/plain");
            echo(file_get_contents($log));
        }
    });
    $app->post("/clear", function() {
        $owner = Auth::isStaffLoggedin("owner");
        $password = requestData("password");
        if ($password) {
            $owner->makeVisible(['password']);
            // Valid password, delete
            if (password_verify($password, $owner->password)) {
                User::truncate();
                Profile::truncate();
                Gallery::truncate();
                $uploadsDir = app_paths("uploads_path");
                $dirs = glob($uploadsDir . "/*", GLOB_ONLYDIR);
                foreach ($dirs as $dir) {
                    Misc::recursiveRemove($dir);
                }
                response("Reseted successfully");
            }
            else {
                throwErr("Invalid password", 400);
            }
        }
        else {
            throwErr("No password sent", 400);
        }
    });
});
