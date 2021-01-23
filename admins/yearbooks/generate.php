<?php
require_once("../../headers.php");
require_once("../../functions.php");
require_once("../../auth.php");
require_once("../../helpers/db.php");
require_once("../../config/config.php");
require_once("../../helpers/email.php");
require_once("../../helpers/zip.php");
require_once("themes.php");
require_once("../../lang/lang.php");

class GenYB {
    private $conn;
    private $profileinfo;
    private $themes;
    private $acyear;
    private $dt;
    private $baseurl;
    private $emails = [];
    function __construct($profileinfo, $themes) {
        $this->db = new DB;
        $this->profileinfo = $profileinfo;
        $this->themes = $themes;
        $this->acyear = date("Y",strtotime("-1 year"))."-".date("Y");
        $this->dt = new DateTime("now");
    }
    
    // Copy files
    private function recursivecopy($source, $dest){
        foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),\RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } 
            else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    // Check if theme is valid
    public function initialCheck($theme) {
        if (in_array($theme, $this->themes)) {
            return true;
        }
        return false;
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
    
    private function getUser($userid) {
        $stmt = $this->db->prepare("SELECT fullname, `type` FROM users WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $user = [
            "name" => $row["fullname"],
            "type" => $row["type"]
        ];
        $stmt->close();
        return $user; 
    }
    
    // Get users from DB
    public function getUsers() {
        $users = [
            "students" => [],
            "teachers" => []
        ];
        $stmt = $this->db->prepare("SELECT id, userid, photo, video, link, quote, uploaded, subject FROM profiles WHERE schoolid=? AND schoolyear=?");
        $stmt->bind_param("is", $this->profileinfo["schoolid"], $this->profileinfo["year"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $id = $row["id"];
            $user = $this->getUser($row["userid"]);
            $dir = $user["type"]."/";
            $new_user = [
                "userid" => $id,
                "fullname" => $user["name"],
                "type" => $user["type"],
                "photo" => $dir.$id.'/'.$row["photo"],
                "video" => $dir.$id.'/'.$row["video"],
                "url" => $row["link"],
                "quote" => $row["quote"], // User quote
                "date" => $row["uploaded"]
            ];
            if ($user["type"] == "teachers") {
                $new_user["subject"] = $row["subject"];
                array_push($users["teachers"], $new_user);
            }
            else {
                array_push($users["students"], $new_user);
            }
        }
        $stmt->close();
        return $users;
    }

    // Get gallery from DB
    public function getGallery() {
        $gallery = [];
        // Gallery
        $stmt = $this->db->prepare("SELECT name, description, type FROM gallery WHERE schoolid=? AND schoolyear=?");
        $stmt->bind_param("is", $this->profileinfo["schoolid"], $this->profileinfo["year"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $gallery = array();
        $gallery_dir = 'gallery/';
        while($row = mysqli_fetch_assoc($result)) {
            $gallery[] = [
                "path" => $gallery_dir.$row["name"],
                "description" => $row["description"],
                "type" => $row["type"]
            ];
        }
        $stmt->close();
        return $gallery;
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
        $this->recursivecopy($source, $this->baseurl);
        
        // Copy all theme-specific assets
        $theme = $_POST["theme"];
        $source = "themes/{$theme}/";
        $this->recursivecopy($source, $this->baseurl);
        
        // Copy all common assets
        $source = "themes/common/";
        $this->recursivecopy($source, $this->baseurl);
        
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
        $ybinfo = [
            "schoolname" => $this->profileinfo["schoolname"],
            "year" => $this->profileinfo["year"],
            "acyear" => $this->acyear,
            "ybdate" => $this->dt->getTimestamp()
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
        HZip::zipDir($this->baseurl, $this->baseurl."/yearbook.zip");
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
    $genyb = new GenYB($profileinfo, $themes);
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
