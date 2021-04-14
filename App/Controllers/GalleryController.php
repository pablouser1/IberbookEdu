<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Gallery;
use App\Helpers\Streamer;
use App\Helpers\Chunk;
use App\Helpers\Misc;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UploadGallery {
    private $profile;
    private $baseurl;
    private $type;
    public $chunk;
    function __construct($profile) {
        $this->profile = $profile;
        $this->baseurl = storage_path("app/uploads/".$this->profile->group_id."/gallery/");
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
	public function all()
	{
        $profile = Auth::isProfileLoggedin();
        $gallery = Gallery::all()->where("group_id", "=", $profile->group_id);
        response($gallery);
	}

    // Stream item
    public function one($id) {
        $profile = Auth::isProfileLoggedin();
        try {
            $gallery = Gallery::findOrFail($id);
            $path = storage_path()."/app/uploads/".$profile->group_id."/gallery/".$gallery->name;
            $streamer = new Streamer($path);
            $streamer->start();
        }
        catch (ModelNotFoundException $e) {
            throwErr("Not found", 404);
        }
    }

    public function upload() {
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        if ($user->isMod()) {
            $upload = new UploadGallery($profile);
            $result = $upload->startUpload($_FILES);
            if ($result) {
                if ($upload->chunk->hasAllChunks()) {
                    $upload->chunk->merge();
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

    public function delete() {
        $user = Auth::isUserLoggedin();
        $profile = Auth::isProfileLoggedin();
        if ($user->isMod()) {
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
            throwErr("You don't have permissions", 401);
        }
    }
}
