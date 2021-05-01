<?php
namespace Helpers;
use Helpers\FullUser;
use Helpers\Zip;
use Models\Gallery;
use Models\Theme;
use Models\Yearbook;
use Leaf\FS;
class GenYB {
    private $profile;
    public $theme;
    private $acyear;
    private $themedir;
    private $themeCommon;
    private $yearbookdir;
    private $upload;
    function __construct($profile) {
        $this->profile = $profile;
        $this->acyear = acyear();
        $this->upload = storage_path("app/uploads/".$profile->group_id);
        $this->themeCommon = storage_path("app/themes/common");
    }

    // Check if theme is valid
    public function initialCheck($id) {
        $theme = Theme::where("id", "=", $id)->first();
        if ($theme) {
            $this->theme = $theme;
            $this->themedir = storage_path("app/themes/".$theme->name);
            return true;
        }
        return false;
    }

    // Write yearbook to database
    public function writeToDB($banner) {
        $yearbook = new Yearbook;
        $yearbook->group_id = $this->profile->group_id;
        $yearbook->acyear = $this->acyear;
        $yearbook->banner = $banner;
        $yearbook->save();
        $this->yearbookdir = storage_path("app/yearbooks/".$yearbook->id);
        return $yearbook->id;
    }

    // Get users from DB
    public function getUsers() {
        $users = [
            "students" => [],
            "teachers" => []
        ];
        $tempUsers = FullUser::full($this->profile->group_id);
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
        $items = Gallery::all()->where("group_id", "=", $this->profile->group_id);
        return $items;
    }

    // Create yearbook dir if it doesn't exist
    public function createDirs() {
        if(!is_dir($this->yearbookdir)) {
            mkdir($this->yearbookdir, 0755, true);
            mkdir($this->yearbookdir."/assets", 0755, true);
        }
    }

    // Copy necessary files to yearbook dir
    public function copyFiles() {
        // Copy all user uploaded files
        $source = $this->upload;
        FS::superCopy($source, $this->yearbookdir);

        $source = $this->themedir;
        FS::superCopy($source, $this->yearbookdir);

        // Copy all common assets
        $source = $this->themeCommon;
        FS::superCopy($source, $this->yearbookdir);
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
            "schoolname" => $this->profile->group->school->name,
            "year" => $this->profile->group->name,
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
        file_put_contents("{$this->yearbookdir}/scripts/data.js", $data);
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
                move_uploaded_file($tmpFilePath, "$this->yearbookdir/assets/$banner");
            }
        }
    }

    // Zip Yearbook
    public function zipYearbook() {
        // Zip yearbook
        Zip::zipDir($this->yearbookdir, $this->yearbookdir."/yearbook.zip");
    }
}
