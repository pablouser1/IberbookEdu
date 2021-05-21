<?php

namespace Controllers;

use Helpers\Auth;
use Models\Staff;

class StaffController extends \Leaf\ApiController {
	public function create() {
        $staff = Auth::isStaffLoggedin();
        $newStaff = requestData("staff");
        if ($newStaff) {
            $dbStaff = new Staff;
            $dbStaff->username = $newStaff["username"];
            $dbStaff->password = password_hash($newStaff["password"], PASSWORD_DEFAULT);
            $dbStaff->role = "owner";
            $dbStaff->save();
            response("Staff created sucessfully");
        }
        else {
            throwErr("No staff sent", 400);
        }
	}

    public function delete($id) {
        $staff = Auth::isStaffLoggedin();
        $deleteStaff = Staff::where("id", "=", $id)->first();
        if ($deleteStaff) {
            $deleteStaff->delete();
            response("Success");
        }
        else {
            throwErr("Staff not found", 400);
        }
    }
}
