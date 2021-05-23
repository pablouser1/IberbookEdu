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

    public function delete() {
        $staff = Auth::isStaffLoggedin();
        $users = requestData("staff");
        if ($users && !empty($users)) {
            foreach ($users as $user) {
                $deleteStaff = Staff::where("id", "=", $user)->first();
                if ($deleteStaff) {
                    $deleteStaff->delete();
                }
                else {
                    throwErr("Staff not found", 400);
                }
            }
            response("Staff deleted successfully");
        }
        else {
            throwErr("Staff not sent", 400);
        }
    }
}
