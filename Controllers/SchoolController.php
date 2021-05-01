<?php

namespace Controllers;

use Helpers\Auth;
use Models\School;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SchoolController extends \Leaf\ApiController
{
    public function all() {
        $groups = School::all();
        response($groups);
    }

    public function one($id) {
        try {
            $group = School::findOrFail($id);
            response($group);
        }
        catch (ModelNotFoundException $e) {
            throwErr("School not found", 404);
        }
    }

    public function create() {
        $newSchool = $_POST["school"];
        $staff = Auth::isStaffLoggedin();
        $school = new School;
        $school->name = $newSchool;
        $school->save();
        response()->redirect("staff/owner/dashboard");
    }

    public function delete($id) {
        $staff = Auth::isStaffLoggedin();
        $school = School::where("id", "=", $id)->first();
        if ($school) {
            $school->delete();
            response("Success");
        }
        else {
            throwErr("That school doesn't exist", 400);
        }
    }
}
