<?php

namespace App\Controllers;

use App\Helpers\Auth;
use Leaf\Form;

class AccountController extends \Leaf\ApiController
{
	public function user() {
        $data = requestBody();
        $isValid = Form::validate([
            "username" => "validusername",
            "password" => "required"
        ]);
        if ($isValid) {
            $loggedIn = Auth::loginUser($data["username"], $data["password"]);
            if (!empty($loggedIn)) {
                response($loggedIn);
            }
            else {
                throwErr("Error while logging in", 401);
            }
        }
	}

    public function profile($id) {
        $user = Auth::isUserLoggedin();
        $loggedIn = Auth::loginProfile($id, $user);
        if (!empty($loggedIn)) {
            response($loggedIn);
        }
        else {
            throwErr([
                "error" => "Error while logging in"
            ], 401);
        }
    }

    public function staff() {
        $data = requestBody();
        $isValid = Form::validate([
            "username" => "validusername",
            "password" => "required"
        ]);
        if ($isValid) {
            $loggedIn = Auth::loginStaff($data["username"], $data["password"]);
            if (!empty($loggedIn)) {
                $type = $loggedIn->role;
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
