<?php

namespace Controllers;

use Helpers\Auth;
use Models\Profile;
use Helpers\Streamer;
use Helpers\UploadMedia;
use Helpers\UploadMisc;

class ProfileController extends \Leaf\ApiController {
	public function all() {
        $profiles = Profile::all();
        response($profiles);
	}

    public function me() {
        $user = Auth::isUserLoggedin();
        $profiles = Profile::all()->where("user_id", "=", $user->id);
        if ($profiles->isEmpty()) {
            throwErr("No profiles found, pleas contact the web owner", 404);
        }
        response($profiles);
    }

    public function current() {
        $profile = Auth::isProfileLoggedin();
        response($profile);
    }

    private static function prepareStream($id) {
        $user = Auth::isUserLoggedin();
        $myProfile = Auth::isProfileLoggedin();
        $profile = Profile::findOrFail($id);
        if (Auth::amIAllowed($user->role, $myProfile, $profile)) {
            return $profile;
        }
        return false;
    }

    public function photo($id) {
        $profile = self::prepareStream($id);
        if ($profile) {
            $baseurl = profile_uploads_path($profile->group_id, $profile->id) . "/" . $profile->photo;
            $streamer = new Streamer($baseurl);
            $streamer->start();
        }
    }

    public function video($id) {
        $profile = self::prepareStream($id);
        if ($profile) {
            $baseurl = profile_uploads_path($profile->group_id, $profile->id) . "/" . $profile->video;
            $streamer = new Streamer($baseurl);
            $streamer->start();
        }
    }

    public function uploadMedia() {
        $logger = app()->logger();
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        $upload = new UploadMedia($profile);
        $result = $upload->startUpload($_FILES);
        if ($result) {
          if ($upload->chunk->hasAllChunks()) {
            $upload->chunk->merge($upload->baseurl);
            $upload->setToDB($result);
            $logger->info("User {$user->id} successfully uploaded media for profile {$profile->id}");
            response([
                "code" => "C"
            ]);
          }
          else {
            response([
                "code" => "MORE"
            ]);
          }
        }
        else {
            $logger->error("Error uploading media by user {$user->id} for profile {$profile->id}");
            response([
                "code" => "E",
                "error" => "Error uploading media"
            ]);
        }
    }

    public function uploadMisc() {
        $logger = app()->logger();
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        $upload = new UploadMisc($profile);
        $uploadResult = $upload->startUpload();
        if ($uploadResult) {
            $logger->info("User {$user->id} successfully uploaded misc items for profile {$profile->id}");
            response("Uploaded successfully");
        }
        else {
            $logger->error("Error uploading misc items by user {$user->id} for profile {$profile->id}");
            throwErr("Error while uploading", 500);
        }
    }

    public function deleteItems($id) {
        $logger = app()->logger();
        $elements = $_POST["elements"];
        $user = Auth::isUserLoggedin();
        $myProfile = Auth::isProfileLoggedin();
        $profile = Profile::where("id", "=", $id)->first();
        if ($profile) {
            if (Auth::amIAllowed($user->role, $myProfile, $profile)) {
                foreach ($elements as $element) {
                    switch ($element) {
                        case "photo":
                            $file = profile_uploads_path($profile->group_id, $profile->id) . "/" . $profile->photo;
                            $profile->photo = null;
                            unlink($file);
                            break;
                        case "video":
                            $file = profile_uploads_path($profile->group_id, $profile->id) . "/" . $profile->video;
                            $profile->video = null;
                            unlink($file);
                            break;
                        case "quote":
                            $profile->quote = null;
                            break;
                        case "link":
                            $profile->link = null;
                            break;
                    }
                }
                $profile->save();
                $logger->info("User {$user->id} deleted elements from profile {$profile->id}");
                response("Deleted succesfully");
            }
            else {
                throwErr("You are not allowed", 403);
            }
        }
        else {
            throwErr("Profile not found", 404);
        }
    }
}
