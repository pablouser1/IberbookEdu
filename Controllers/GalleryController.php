<?php

namespace Controllers;

use Helpers\Auth;
use Models\Gallery;
use Helpers\Streamer;
use Helpers\Chunk;
use Helpers\Misc;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UploadGallery {
    private $profile;
    private $baseurl;
    private $type;
    public $chunk;
    function __construct($profile) {
        $this->profile = $profile;
        $this->baseurl = group_gallery_path($this->profile->group_id);
        $this->chunk = new Chunk;
    }

    public function startUpload($files) {
        $result = false;
        // Create necessary dirs first
        $this->createDirs();

        // Photo
        if (isset($files['photo'])) {
            $this->type = "photo";
            $allowed_pic = array('gif', 'png', 'jpg', 'jpeg');
            $ext = pathinfo($files["photo"]["name"], PATHINFO_EXTENSION);
            // If the extension is not in the array skip
            if (in_array($ext, $allowed_pic)) {
                $result = $this->chunk->uploadChunk($this->baseurl, $files["photo"]);
            }
        }
        // Video
        elseif (isset($files['video'])) {
            $this->type = "video";
            $allowed_vid = array('mp4', 'webm');
            $ext = pathinfo($files["video"]["name"], PATHINFO_EXTENSION);
            // If the extension is not in the array skip
            if (in_array($ext, $allowed_vid)) {
                $result = $this->chunk->uploadChunk($this->baseurl, $files["video"]);
            }
        }
        return $result;
    }

    private function createDirs() {
        if (!is_dir($this->baseurl)){
            mkdir($this->baseurl, 0755, true);
        }
    }

    public function setToDB($item) {
        $gallery = new Gallery;
        $gallery->name = $item;
        $gallery->group_id = $this->profile->group_id;
        $gallery->type = $this->type;
        $gallery->save();
        return $gallery->id;
    }
}

class GalleryController extends \Leaf\ApiController
{
	public function all(int $group_id) {
        $profile = Auth::isProfileLoggedin();
        if ($profile->group_id === $group_id) {
            $gallery = Gallery::all()->where("group_id", "=", $profile->group_id);
            response($gallery);
        }
        else {
            throwErr("You don't have permissions to access this gallery", 403);
        }
	}

    // Stream item
    public function one(int $group_id, int $item_id) {
        $profile = Auth::isProfileLoggedin();
        if ($profile->group_id === $group_id) {
            try {
                $gallery = Gallery::findOrFail($item_id);
                $path = group_gallery_path($group_id)."/".$gallery->name;
                $streamer = new Streamer($path);
                $streamer->start();
            }
            catch (ModelNotFoundException $e) {
                throwErr("Not found", 404);
            }
        }
    }

    public function upload(int $group_id) {
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        if ($user->isMod() && $profile->group_id === $group_id) {
            $upload = new UploadGallery($profile);
            $result = $upload->startUpload($_FILES);
            if ($result) {
                if ($upload->chunk->hasAllChunks()) {
                    $upload->chunk->merge($upload->baseurl);
                    $id = $upload->setToDB($result);
                    if ($id) {
                        json([
                            "code" => "C",
                            "id" => $id
                        ]);
                    }
                    else {
                        json([
                            "code" => "E",
                            "error" => "Unable to write item to database"
                        ]);
                    }
                }
                else {
                    json([
                        "code" => "MORE"
                    ]);
                }
            }
            else {
                json([
                    "code" => "E",
                    "error" => "Error uploading gallery"
                ]);
            }
        }
    }

    public function delete($group_id) {
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        if ($user->isMod() && $profile->group_id === $group_id) {
            $gallery = Gallery::all()->where("group_id", "=", $profile->group_id);
            foreach ($gallery as $item) {
                $item->delete();
            }
            $dir = storage_path("app/uploads/".$profile->group_id."/gallery");
            Misc::recursiveRemove($dir);
            json([
                "message" => "Deleted successfully"
            ]);
        }
        else {
            throwErr("You don't have permissions", 403);
        }
    }
}
