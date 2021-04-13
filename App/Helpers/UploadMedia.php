<?php

namespace App\Helpers;

use App\Helpers\Chunk;
use App\Models\Profile;

class UploadMedia
{
    private $profileinfo;
    private $baseurl;
    public $chunk;

    function __construct($profileinfo) {
        $this->chunk = new Chunk;
        $this->profileinfo = $profileinfo;
        $this->baseurl = storage_path("app/uploads/") . $this->profileinfo->group_id . "/users/" . $this->profileinfo->id . "/";
    }

    public function startUpload($files) {
        $result = false;
        // Create necessary dirs first
        $this->createDirs();

        // Get not currently uploaded elements
        $current = $this->getNotUploaded();

        // Photo
        if (isset($files['photo'])) {
            $this->type = "photo";
            // Not in remaining array, replacing
            if ($current->photo) {
                $this->deleteMedia();
            }
            $allowed_pic = array('gif', 'png', 'jpg', 'jpeg');
            $ext = pathinfo($files["photo"]["name"], PATHINFO_EXTENSION);
            // If the extension is not in the array create error message
            if (in_array($ext, $allowed_pic)) {
                $result = $this->chunk->uploadChunk($this->baseurl, $files["photo"]);
            }
        }
        // Video
        elseif (isset($files['video'])) {
            $this->type = "video";
            if ($current->video) {
                $this->deleteMedia();
            }
            $allowed_vid = array('mp4', 'webm');
            $ext = pathinfo($files["video"]["name"], PATHINFO_EXTENSION);
            // If the extension is not in the array create error message
            if (in_array($ext, $allowed_vid)) {
                $result = $this->chunk->uploadChunk($this->baseurl, $files["video"]);
            }
        }
        return $result;
    }

    private function createDirs() {
        if (!is_dir($this->baseurl)) {
            mkdir($this->baseurl, 0755, true);
        }
    }

    public function setToDB($filename) {
        if ($this->type === "video") {
            $this->profileinfo->video = $filename;
        }
        elseif($this->type === "photo") {
            $this->profileinfo->photo = $filename;
        }
        $this->profileinfo->save();
    }

    // Get elements not uploaded yet
    private function getNotUploaded() {
        $remain = Profile::select("photo", "video")->where("id", "=", $this->profileinfo->id)->first();
        return $remain;
    }

    public function deleteMedia() {
        $name = null;
        if ($this->type === "video") {
            $name = $this->profileinfo->video;
            $this->profileinfo->video = null;
        }
        elseif($this->type === "photo") {
            $name = $this->profileinfo->photo;
            $this->profileinfo->photo = null;
        }

        $this->profileinfo->save();

        unlink($this->baseurl . $name);
    }
}
