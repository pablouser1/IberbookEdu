<?php

namespace Controllers;
use Helpers\Auth;
use Helpers\GenYB;
use Helpers\Misc;
use Helpers\Zip;
use Models\Yearbook;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Models\Theme;

class YearbookController extends \Leaf\ApiController {
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
        $yearbook = Yearbook::where("id", "=", $id)->first();
        if ($yearbook) {
            json($yearbook);
        }
        else {
            throwErr("Yearbook not found", 404);
        }
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
        if ($yearbook) {
            response($yearbook);
        }
        else {
            json($yearbook, 204);
        }
    }

    public function generate(int $group_id) {
        ignore_user_abort(1);
        set_time_limit(0);
        $logger = app()->logger();
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        if ($user->isMod() && $profile->group_id === $group_id) {
            $genyb = new GenYB($profile->group_id, $profile->group->name, $profile->group->school->name);
            $banner = null;
            if (isset($_FILES['banner'])) {
                $banner = $_FILES['banner']['name'];
            }
            // Get info
            $users = $genyb->getUsers();
            $students = $users["students"];
            $teachers = $users["teachers"];
            if (empty($students) && empty($students)) {
                throwErr("Not enough users, at least one student or one teacher with image and video");
            }
            // Write yearbook to DB
            $ybid = $genyb->writeToDB($banner);
            $gallery = $genyb->getGallery();
            // Create dirs
            $genyb->createDirs();
            // Copy all files to working dir
            $genyb->copyFiles();
            // Create and copy config files
            $genyb->setConfig($students, $teachers, $gallery, $banner);
            // Everyting went OK
            $logger->info("Yearbook of group {$group_id} created by {$user->username}");
            json([
                "id" => $ybid,
                "message" => "Yearbook sent successfully"
            ]);
        }
        else {
            throwErr("You are not a mod", 403);
        }

    }

    public function view($id) {
        if (Yearbook::where("id", "=", $id)->exists()) {
            $themeName = requestData("theme");
            if (!empty($themeName)) {
                $theme = Theme::where("name", "=", $themeName)->first();
                if ($theme) {
                    $url = getenv("APP_URL") . "/";
                    $themeDir = $url . get_theme($theme->name);
                    $common = $url . "/pages/themes/common/";
                    $data = $url . group_yearbook_path($id);
                    render("themes/{$theme->name}/index", [
                        "common" => $common,
                        "theme" => $themeDir,
                        "data" => $data
                    ]);
                }
                else {
                    response()->markup("Invalid theme", 400);
                }
            }
            else {
                response()->markup("You need to send a theme", 400);
            }
        }
        else {
            response()->markup("This yearbook doesn't exist", 404);
        }
    }

    // Creates zip of yearbook
    public function download($id) {
        set_time_limit(300);
        $yearbook = Yearbook::where("id", "=", $id)->first();
        if ($yearbook) {
            $themeName = requestData("theme");
            if (!empty($themeName)) {
                $theme = Theme::where("name", "=", $themeName)->first();
                if ($theme && $theme->details["zip"]) {
                    $url = getenv("APP_URL") . "/";
                    $zipName = "yearbook_{$id}_{$theme->name}.zip";
                    $zipPath = storage_path("framework/zips") . "/" . $zipName;
                    if (file_exists($zipPath)) {
                        // Zip already exists, skip zipping and redirect to download
                        response()->redirect($url . $zipPath);
                    }
                    else {
                        // Zip doesn't exist, create one
                        // Using relative paths, user will download this into one single zip file
                        $html = render_text("themes/default/index", [
                            "common" => "./",
                            "theme" => "./",
                            "data" => "./"
                        ]);
                        $yearbookDir = group_yearbook_path($id);
                        $themeDir = get_theme($theme->name);
                        $commonDir = get_theme("common");
                        $zipPath = storage_path("framework/zips") . "/" . $zipName;
                        // Zipping all elements of yearbook
                        Zip::zipDir($yearbookDir, $zipPath);
                        Zip::zipDir($themeDir, $zipPath, [
                            "index.latte", "theme.json"
                        ]);
                        Zip::zipDir($commonDir, $zipPath);
                        Zip::zipString($html, "index.html", $zipPath);
                        response()->redirect($url . $zipPath);
                    }
                }
                else {
                    response()->markup("Theme not valid or can't be zipped", 400);
                }
            }
            else {
                response()->markup("You need to send a theme", 400);
            }
        }
        else {
            response()->markup("This yearbook doesn't exist", 404);
        }
    }

    public function delete($id) {
        $acyear = acyear();
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        $yearbook = Yearbook::where("id", "=", $id)->first();
        if ($yearbook) {
            if ($user->isMod() && $profile->group_id === $yearbook->group_id && $acyear === $yearbook->acyear) {
                $zipDir = storage_path("framework/zips");
                // Delete zips (if any)
                $zips = glob($zipDir . "/yearbook_" . $yearbook->id . "_*.zip");
                if ($zips) {
                    foreach ($zips as $zip) {
                        unlink($zip);
                    }
                }
                // Delete from database
                $yearbook->delete();
                // Delete files
                Misc::recursiveRemove(group_yearbook_path($id));
                // Delete zips (if any)
                response([
                    "message" => "Deleted successfully"
                ]);
            }
            else {
                throwErr("You don't have permissions", 403);
            }
        }
        else {
            throwErr("Yearbook not found", 404);
        }
    }

    public function vote($id) {
        $logger = app()->logger();
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
        $logger->info("User {$user->username} voted for yearbook {$yearbook->id}");
        json([
            "message" => "Voted sucessfully"
        ]);
    }
}
