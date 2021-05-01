<?php

namespace Controllers;

use Helpers\Auth;
use Models\Staff;

class StaffController extends \Leaf\ApiController
{
	public function create() {
        $staff = Auth::isStaffLoggedin();
        $newStaff = $_POST["staff"];
        $dbStaff = new Staff;
        $dbStaff->username = $newStaff["username"];
        $dbStaff->password = password_hash($newStaff["password"], PASSWORD_DEFAULT);
        $dbStaff->role = "owner";
        $dbStaff->save();
        response()->redirect("../staff/owner/dashboard");
	}

    public function delete($id) {
        $staff = Auth::isStaffLoggedin();
        $deleteStaff = Staff::where("id", "=", $id)->first();
        if ($deleteStaff) {
            $deleteStaff->delete();
        }
        response("Success");
    }
}
