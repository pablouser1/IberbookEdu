<?php

namespace Controllers;

use Helpers\Auth;
use Models\Group;
use Helpers\FullUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GroupController extends \Leaf\ApiController
{
    public function all() {
        $groups = Group::all();
        response($groups);
    }

    public function one($id) {
        try {
            $group = Group::findOrFail($id);
            response($group);
        }
        catch (ModelNotFoundException $e) {
            throwErr("Group not found", 404);
        }
    }

    public function members() {
        $members = [];
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        if ($user->isMod()) {
            $members = FullUser::full($profile->group_id);
        }
        else {
            $members = FullUser::basic($profile->group_id);
        }
        response($members);
    }

    public function create() {
        $newGroup = $_POST["group"];
        $staff = Auth::isStaffLoggedin();
        $group = new Group;
        $group->name = $newGroup["name"];
        $group->school_id = $newGroup["school"];
        $group->save();
        response()->redirect("../staff/owner/dashboard");
    }

    public function delete($id) {
        $staff = Auth::isStaffLoggedin();
        $group = Group::where("id", "=", $id)->first();
        if ($group) {
            $group->delete();
            response("Success");
        }
        else {
            throwErr("That group doesn't exist", 400);
        }
    }
}
