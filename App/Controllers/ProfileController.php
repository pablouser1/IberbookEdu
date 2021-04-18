<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Profile;
use App\Helpers\Streamer;
use App\Helpers\UploadMedia;
use App\Helpers\UploadMisc;

class ProfileController extends \Leaf\ApiController
{
	public function all()
	{
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
            $baseurl = storage_path()."/app/uploads/". $profile->group_id . "/users/" . $profile->id . "/" . $profile->photo;
            $streamer = new Streamer($baseurl);
            $streamer->start();
        }
    }

    public function video($id) {
        $profile = self::prepareStream($id);
        if ($profile) {
            $baseurl = storage_path()."/app/uploads/". $profile->group_id . "/users/" . $profile->id . "/" . $profile->video;
            $streamer = new Streamer($baseurl);
            $streamer->start();
        }
    }

    public function uploadMedia() {
        $profile = Auth::isProfileLoggedin();
        $upload = new UploadMedia($profile);
        $result = $upload->startUpload($_FILES);
        if ($result) {
          if ($upload->chunk->hasAllChunks()) {
            $upload->chunk->merge();
            $upload->setToDB($result);
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
            response([
                "code" => "E",
                "error" => "Error uploading file"
            ]);
        }
    }

    public function uploadMisc() {
        $profile = Auth::isProfileLoggedin();
        $upload = new UploadMisc($profile);
        $uploadResult = $upload->startUpload();
        if ($uploadResult) {
            response("Uploaded successfully");
        }
        else {
            throwErr("Error while uploading", 500);
        }
    }

    public function deleteItems($id) {
        $elements = $_POST["elements"];
        $user = Auth::isUserLoggedin();
        $myProfile = Auth::isProfileLoggedin();
        $profile = Profile::where("id", "=", $id)->first();
        if ($profile) {
            if (Auth::amIAllowed($user->role, $myProfile, $profile)) {
                foreach ($elements as $element) {
                    switch ($element) {
                        case "photo":
                            $file = storage_path("app/uploads/".$profile->group_id."/users/".$profile->id."/".$profile->photo);
                            $profile->photo = null;
                            unlink($file);
                            break;
                        case "video":
                            $file = storage_path("app/uploads/".$profile->group_id."/users/".$profile->id."/".$profile->video);
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
