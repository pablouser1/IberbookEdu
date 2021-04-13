<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Misc;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends \Leaf\ApiController
{
	public function all()
	{
        $users = User::all();
        response($users);
	}

    public function one($id) {
        try {
            $user = User::findOrFail($id);
            response($user);
        }
        catch (ModelNotFoundException) {
            throwErr("User not found", 404);
        }
    }

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
                $password = Misc::random_password(12);
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $newUser->username = $user["username"];
                $newUser->password = $hashed_password;
                $newUser->type = $user["type"];
                $newUser->name = $user["name"];
                $newUser->surname = $user["surname"];
                $newUser->save();
                foreach ($user["groups"] as $group) {
                    $newProfile = new Profile;
                    $newProfile->user_id = $newUser->id;
                    $newProfile->group_id = $group;
                    $newProfile->save();
                }
                $response[] = [
                    "username" => $user["username"],
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
