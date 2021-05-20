<?php
namespace Helpers;
use Helpers\FullUser;
use Models\Gallery;
use Models\Yearbook;
use Leaf\FS;

class GenYB {
    private $groupId;
    private $groupName;
    private $acyear;
    private $dataDir;
    private $upload;
    function __construct(int $groupId, string $groupName, string $schoolName) {
        $this->groupId = $groupId;
        $this->groupName = $groupName;
        $this->schoolName = $schoolName;
        $this->acyear = acyear();
        $this->upload = group_uploads_path($this->groupId);
    }

    // Write yearbook to database
    public function writeToDB($banner) {
        $yearbook = new Yearbook;
        $yearbook->group_id = $this->groupId;
        $yearbook->acyear = $this->acyear;
        $yearbook->banner = $banner;
        $yearbook->save();
        $this->dataDir = group_yearbook_path($yearbook->id);
        return $yearbook->id;
    }

    // Get users from DB
    public function getUsers() {
        $users = [
            "students" => [],
            "teachers" => []
        ];
        $tempUsers = FullUser::full($this->groupId);
        foreach ($tempUsers as $tempUser) {
            // Users need to have at least a photo and a video
            if ($tempUser["photo"] && $tempUser["video"]) {
                if ($tempUser["type"] == "teachers") {
                    array_push($users["teachers"], $tempUser);
                }
                else {
                    array_push($users["students"], $tempUser);
                }
            }
        }
        return $users;
    }

    // Get gallery from DB
    public function getGallery() {
        $items = Gallery::all()->where("group_id", "=", $this->groupId);
        return $items;
    }

    // Create yearbook dir if it doesn't exist
    public function createDirs() {
        if(!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }

    // Copy necessary files to yearbook dir
    public function copyFiles() {
        // Copy all user uploaded files
        $source = $this->upload;
        FS::superCopy($source, $this->dataDir);
    }

    // Save yearbook config as .js file
    public function setConfig($students, $teachers, $gallery, $banner) {
        // Teachers
        $teachers_js = json_encode($teachers, JSON_PRETTY_PRINT);
        // Students
        $students_js = json_encode($students, JSON_PRETTY_PRINT);
        // Gallery
        $gallery_js = json_encode($gallery, JSON_PRETTY_PRINT);
        // Yearbook info
        $dt = d();
        $ybinfo = [
            "schoolname" => $this->schoolName,
            "year" => $this->groupName,
            "acyear" => $this->acyear,
            "ybdate" => $dt->now()
        ];
        $ybinfo["banner"] = $banner;

        if (!empty($banner)) {
            $this->uploadBanner();
        }
        $ybinfo_js = json_encode($ybinfo, JSON_PRETTY_PRINT);
        // Data to be written in js file
        $data = "
        // Config auto-generated using IberbookEdu, DO NOT MODIFY MANUALLY

        // Teachers
        const teachers_js = {$teachers_js};
        // Students
        const students_js = {$students_js};
        // Gallery
        const gallery_js = {$gallery_js};
        // Yearbook info
        const ybinfo_js = {$ybinfo_js};
        ";
        file_put_contents("{$this->dataDir}/data.js", $data);
    }

    // Upload banner if given
    private function uploadBanner() {
        // Copy banner
        $banner = $_FILES['banner']["name"];
        $tmpFilePath = $_FILES['banner']['tmp_name'];
        if($tmpFilePath != "") {
            $uploadName = $banner;
            $ext = pathinfo($uploadName, PATHINFO_EXTENSION);
            if (in_array($ext, ["jpg", "jpeg", "png", "gif"])) {
                move_uploaded_file($tmpFilePath, $this->dataDir . "/" . $banner);
            }
        }
    }
}
