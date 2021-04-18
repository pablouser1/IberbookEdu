<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Misc;
use App\Models\Profile;
use App\Models\User;

class UserController extends \Leaf\ApiController
{
    public function me() {
        $user = Auth::isUserLoggedin();
        response ($user);
    }

    public function create() {
        $users = false;
        if (isset($_POST["users"])) {
            $users = $_POST["users"];
        }
        elseif (isset($_FILES['json'])) {
            $file = file_get_contents($_FILES['json']['tmp_name']);
            $users = json_decode($file, true);
        }
        if ($users) {
            $response = [];
            foreach ($users as $user) {
                $newUser = new User;
                $birthday = date("Y-m-d", strtotime($user["birthday"]));
                $username = Misc::generate_username($user["name"], $user["surname"], $birthday);
                $password = Misc::generate_password($user["surname"], $birthday);
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $newUser->username = $username;
                $newUser->password = $hashed_password;
                $newUser->type = $user["type"];
                $newUser->role = $user["role"];
                $newUser->name = $user["name"];
                $newUser->surname = $user["surname"];
                $newUser->save();
                foreach ($user["groups"] as $group) {
                    $newProfile = new Profile;
                    $newProfile->user_id = $newUser->id;
                    $newProfile->group_id = (int) $group;
                    $newProfile->save();
                }
                $response[] = [
                    "username" => $username,
                    "password" => $password,
                    "fullname" => $newUser->fullname
                ];
            }
            response($response);
        }
        else {
            throwErr("Invalid users sent", 400);
        }
    }

    public function delete() {

    }

    public function password() {
        $user = Auth::isUserLoggedin();
        $oldPassword = $_POST["oldPassword"];
        $newPassword = $_POST["newPassword"];
        $user->makeVisible(['password']);
        if (password_verify($oldPassword, $user->password) && Misc::isPasswordValid($newPassword)) {
            $password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $user->password = $password_hash;
            $user->save();
            $user->makeHidden(['password']);
            response("OK");
        }
        else {
            throwErr("Invalid password", 400);
        }
    }
}
