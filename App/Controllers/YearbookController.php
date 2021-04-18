<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\GenYB;
use App\Helpers\Misc;
use App\Models\Yearbook;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class YearbookController extends \Leaf\ApiController
{
	public function all() {
        $data = requestData(["sort", "count"]);
        if (!isset($data["sort"])) {
            $data["sort"] = "votes";
        }
        if (!isset($data["count"])) {
            $data["count"] = 0;
        }
        $yearbooks = Yearbook::all()->sortBy($data["sort"])->skip($data["count"]);
        if ($yearbooks->isEmpty()) {
            throwErr("No more yearbooks", 404);
        }
		response($yearbooks);
	}

    public function one($id) {
        $yearbook = Yearbook::findOrFail($id);
        response($yearbook);
    }

    public function random() {
        $yearbook = Yearbook::inRandomOrder()->first();
        if ($yearbook) {
            response($yearbook);
        }
        else {
            json($yearbook, 204);
        }
    }

    public function me() {
        $acyear = acyear();
        $profile = Auth::isProfileLoggedin();
        $yearbook = Yearbook::where("group_id", "=", $profile->group_id)->where("acyear", "=", $acyear)->first();
        response($yearbook);
    }

    public function generate() {
        ignore_user_abort(1);
        set_time_limit(0);
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        if ($user->isMod()) {
            $genyb = new GenYB($profile);
            if ($genyb->initialCheck($_POST["theme"])) {
                $banner = null;
                if ($genyb->theme->details["banner"] && isset($_FILES['banner'])) {
                    $banner = $_FILES['banner']['name'];
                }
                // Write yearbook to DB
                $ybid = $genyb->writeToDB($banner);
                // Get info
                $users = $genyb->getUsers();
                $students = $users["students"];
                $teachers = $users["teachers"];
                $gallery = $genyb->getGallery();
                // Create dirs
                $genyb->createDirs();
                // Copy all files to working dir
                $genyb->copyFiles();
                // Create and copy config files
                $genyb->setConfig($students, $teachers, $gallery, $banner);
                // Zip Yearbook
                if ($genyb->theme->details["zip"]) {
                    $genyb->zipYearbook();
                }
                // Everyting went OK
                json([
                    "id" => $ybid,
                    "message" => "Yearbook sent successfully"
                ]);
            }
            else {
                throwErr("Invalid theme", 400);
            }
        }
        else {
            throwErr("You are not a mod", 403);
        }

    }

    public function delete($id) {
        $acyear = acyear();
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        $yearbook = Yearbook::where("id", "=", $id)->first();
        if ($yearbook) {
            if ($user->isMod() && $profile->group_id === $yearbook->group_id && $acyear === $yearbook->acyear) {
                $yearbook->delete();
                Misc::recursiveRemove(storage_path("app/yearbooks/".$id));
                response([
                    "message" => "Deleted successfully"
                ]);
            }
            else {
                throwErr("You don't have permissions", 403);
            }
        }
        else {
            throwErr("You don't have a yearbook", 404);
        }
    }

    public function vote($id) {
        $user = Auth::isUserLoggedin();
        try {
            $yearbook = Yearbook::findOrFail($id);
        }
        catch(ModelNotFoundException $e) {
            throwErr("Yearbook not found", 404);
        }
        // If already voted, replace with new one
        if ($user->voted) {
            $oldYearbook = Yearbook::where("id", "=", $user->voted)->first();
            if ($oldYearbook) {
                // Check first if user is trying to vote the same yearbook twice
                if ($user->voted === $oldYearbook->id) {
                    throwErr("You can't vote the same yearbook twice", 400);
                }
            }
        }
        // Add new vote to yearbook
        $yearbook->votes = $yearbook->votes + 1;
        $yearbook->save();
        // Set vote to user
        $user->voted = $yearbook->id;
        $user->save();
        json([
            "message" => "Voted sucessfully"
        ]);
    }
}
