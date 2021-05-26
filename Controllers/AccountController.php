<?php

namespace Controllers;

use Helpers\Auth;
use Leaf\Form;

class AccountController extends \Leaf\ApiController {
	public function user() {
        $logger = app()->logger();
        $data = requestBody();
        $isValid = Form::validate([
            "username" => "validusername",
            "password" => "required"
        ]);
        if ($isValid) {
            $user = Auth::loginUser($data["username"], $data["password"]);
            if (!empty($user)) {
                $logger->info("User {$user->username} successfully logged in");
                response($user);
            }
            else {
                throwErr("Error while logging in", 401);
            }
        }
	}

    public function profile($id) {
        $logger = app()->logger();
        $user = Auth::isUserLoggedin();
        $profile = Auth::loginProfile($id, $user);
        if (!empty($profile)) {
            $logger->info("Profile {$profile->id} from {$user->username} successfully logged in");
            response($profile);
        }
        else {
            throwErr([
                "error" => "Error while logging in"
            ], 401);
        }
    }

    public function staff() {
        $logger = app()->logger();
        $data = requestBody();
        $isValid = Form::validate([
            "username" => "validusername",
            "password" => "required"
        ]);
        if ($isValid) {
            $staff = Auth::loginStaff($data["username"], $data["password"]);
            if (!empty($staff)) {
                $logger->info("Staff {$staff->username} successfully logged in");
                $type = $staff->role;
                response()->redirect("{$type}/dashboard");
            }
            else {
                throwErr("Error while logging in", 401);
            }
        }
    }

    public function logout() {
        Auth::logout();
        response();
    }
}
