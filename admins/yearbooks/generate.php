<?php
require_once("../../headers.php");
require_once("../../functions.php");
require_once("../../auth.php");
require_once("../../helpers/db.php");
require_once("../../config/config.php");
require_once("../../classes/groups.php");
require_once("../../classes/gallery.php");
require_once("../../helpers/email.php");
require_once("../../helpers/zip.php");
require_once("../../lang/lang.php");
require_once("../../classes/themes.php");

class GenYB {
    private $db;
    private $groups;
    private $gallery;
    private $profileinfo;
    private $theme;
    private $themesMng;
    private $themeinfo;
    private $acyear;
    private $baseurl;
    private $emails = [];
    function __construct($profileinfo) {
        $this->db = new DB;
        $this->groups = new Groups;
        $this->gallery = new Gallery;
        $this->themesMng = new Themes;
        $this->profileinfo = $profileinfo;
        $this->acyear = date("Y",strtotime("-1 year"))."-".date("Y");
    }

    // Check if theme is valid
    public function initialCheck($theme) {
        if($themeinfo = $this->themesMng->checkTheme($theme)) {
            $this->theme = $themeinfo;
            return true;
        }
        else {
            return false;
        }
    }

    // Write yearbook to database
    public function writeToDB($banner) {
        // Writes data to DB
        $stmt = $this->db->prepare("INSERT INTO yearbooks(schoolid, schoolname, schoolyear, acyear, banner) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $this->profileinfo["schoolid"], $this->profileinfo["schoolname"], $this->profileinfo["year"], $this->acyear, $banner);
        $stmt->execute();
        $ybid = $stmt->insert_id;
        $stmt->close();
        $this->baseurl = __DIR__."/../../yearbooks/".$ybid;
        return $ybid;
    }
    
    // Get users from DB
    public function getUsers() {
        $users = [
            "students" => [],
            "teachers" => []
        ];
        $tempUsers = $this->groups->getProfilesGroupFull($this->profileinfo["schoolid"], $this->profileinfo["year"]);
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
        $items = $this->gallery->getItems($this->profileinfo["schoolid"], $this->profileinfo["year"]);
        return $items;
    }

    // Create yearbook dir if it doesn't exist
    public function createDirs() {
        if(!is_dir($this->baseurl)) {
            mkdir($this->baseurl, 0755, true);
            mkdir($this->baseurl."/assets", 0755, true);
        }
    }

    // Copy necessary files to yearbook dir
    public function copyFiles() {
        // Copy all user uploaded files
        $source = $GLOBALS["uploadpath"].$this->profileinfo["schoolid"]."/".$this->profileinfo["year"]."/";
        Utils::recursiveCopy($source, $this->baseurl);
        
        $themename = $this->theme["name"];
        $source = "../../themes/{$themename}/";
        Utils::recursiveCopy($source, $this->baseurl);
        
        // Copy all common assets
        $source = "../../themes/common/";
        Utils::recursiveCopy($source, $this->baseurl);
        
        copy("../../favicon.ico",  "{$this->baseurl}/favicon.ico");
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
        $dt = new DateTime("now");
        $ybinfo = [
            "schoolname" => $this->profileinfo["schoolname"],
            "year" => $this->profileinfo["year"],
            "acyear" => $this->acyear,
            "ybdate" => $dt->getTimestamp()
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
        file_put_contents("{$this->baseurl}/scripts/data.js", $data);
    }

    // Upload banner if given
    private function uploadBanner() {
        // Copy banner
        $banner = $_FILES['banner']["name"];
        $tmpFilePath = $_FILES['banner']['tmp_name'];
        if($tmpFilePath != "") {
            $uploadName = $banner;
            $ext = pathinfo($uploadName, PATHINFO_EXTENSION);
            if (!in_array($ext, ["jpg", "jpeg", "png", "gif"])) {
                $response = [
                    "code" => "E",
                    "description" => L::yearbooks_banner
                ];
                sendJSON($response);
            }
            else {
                move_uploaded_file($tmpFilePath, "$this->baseurl/assets/$banner");
            }
        }
    }

    // Zip Yearbook
    public function zipYearbook() {
        // Zip yearbook
        if ($this->theme["zip"]) {
            HZip::zipDir($this->baseurl, $this->baseurl."/yearbook.zip");
        }
    }
}

$auth = new Auth;

$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo && $auth->isUserAdmin($userinfo)) {
    // BANNER //
    $banner = null;
    if ($_FILES['banner']['name']) {
        $banner = $_FILES['banner']['name'];
    }

    // START CLASS //
    $genyb = new GenYB($profileinfo);
    if ($genyb->initialCheck($_POST["theme"])) {
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
        $genyb->zipYearbook();
        // Send emails
        if ($GLOBALS["email"]["enabled"]) {
            $mailclient = new Email($GLOBALS["email"]);
            // Get email from specific group
            $emails = $mailclient->getEmails($profileinfo["schoolid"], $profileinfo["year"]);
            $mailclient->sendYearbook($emails, $ybid);
        }
        // Everyting went OK
        $response = [
            "code" => "C",
            "description" => L::yearbooks_generated
        ];
        sendJSON($response);
    }
    else {
        $response = [
            "code" => "E",
            "error" => L::yearbooks_theme
        ];
    }
}
?>
