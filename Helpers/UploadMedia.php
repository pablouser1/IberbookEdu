<?php

namespace Helpers;

use Helpers\Chunk;
use Models\Profile;

class UploadMedia
{
    private $profile;
    public $temp_dir;
    public $baseurl;
    public $chunk;

    function __construct($profileinfo) {
        $this->chunk = new Chunk;
        $this->profile = $profileinfo;
        $this->baseurl = profile_uploads_path($this->profile->group_id, $this->profile->id);
        $this->temp_dir = sys_get_temp_dir()."/".$this->profile->id;
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
                $result = $this->chunk->uploadChunk($this->temp_dir, $files["photo"]);
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
                $result = $this->chunk->uploadChunk($this->temp_dir, $files["video"]);
            }
        }
        return $result;
    }

    private function createDirs() {
        if (!is_dir($this->baseurl)) {
            mkdir($this->baseurl, 0755, true);
        }
        if (!is_dir($this->temp_dir)) {
            mkdir($this->temp_dir, 0755, true);
        }
    }

    public function setToDB($filename) {
        if ($this->type === "video") {
            $this->profile->video = $filename;
        }
        elseif($this->type === "photo") {
            $this->profile->photo = $filename;
        }
        $this->profile->save();
    }

    // Get elements not uploaded yet
    private function getNotUploaded() {
        $remain = Profile::select("photo", "video")->where("id", "=", $this->profile->id)->first();
        return $remain;
    }

    public function deleteMedia() {
        $name = null;
        if ($this->type === "video") {
            $name = $this->profile->video;
            $this->profile->video = null;
        }
        elseif($this->type === "photo") {
            $name = $this->profile->photo;
            $this->profile->photo = null;
        }

        $this->profile->save();

        unlink($this->baseurl . $name);
    }
}
