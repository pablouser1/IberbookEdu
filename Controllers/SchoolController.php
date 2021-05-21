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
        response("School created successfully");
    }

    public function delete($id) {
        $staff = Auth::isStaffLoggedin();
        $schools = requestData("schools");
        if ($schools && !empty($schools)) {
            foreach ($schools as $school) {
                $tempGroup = School::where("id", "=", $school)->first();
                if ($tempGroup) {
                    $tempGroup->delete();
                }
            }
        }
        else {
            throwErr("No groups sent", 400);
        }
        response("Finished");
    }
}
