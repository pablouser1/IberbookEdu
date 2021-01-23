<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");
require_once("../config/config.php");
require_once("../lang/lang.php");

class UploadGallery {
    private $conn;
    private $profileinfo;
    private $baseurl;
    function __construct($profileinfo) {
        $this->db = new DB;
        $this->profileinfo = $profileinfo;
        $this->baseurl = $GLOBALS["uploadpath"].$this->profileinfo["schoolid"]."/".$this->profileinfo["year"]."/gallery/";
    }

    private function delete_files($target) {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
    
            foreach($files as $file){
                $this->delete_files($file);
            }
    
            rmdir($target);
        } elseif(is_file($target)) {
            unlink($target);  
        }
    }

    public function createDirs() {
        if (!is_dir($this->baseurl)){
            mkdir($this->baseurl, 0755, true);
        }
    }
    public function uploadPhotos($photos) {
        $uploaded_photos = [];
        $allowed_pic = array('gif', 'png', 'jpg', 'jpeg');
        for($i=0; $i<count($photos['name']); $i++) {
            $tmpFilePath = $photos['tmp_name'][$i];
            if($tmpFilePath != ""){
                $filePath = $this->baseurl.$photos['name'][$i];
                $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                if (in_array($ext, $allowed_pic)) {
                    $uploaded_photos[$i]["path"] = basename($filePath);
                    $uploaded_photos[$i]["description"] = htmlspecialchars($_POST["photos_descriptions"][$i]);
                    $uploaded_photos[$i]["type"] = "picture";
                    move_uploaded_file($tmpFilePath, $filePath);
                }
            }
        }
        $this->writeToDB($uploaded_photos);
        return true;
    }

    public function uploadVideos($videos) {
        $uploaded_videos = [];
        $allowed_vid = array('mp4', "webm");
        for($i=0; $i<count($videos['name']); $i++) {
            $tmpFilePath = $videos['tmp_name'][$i];
            if($tmpFilePath != ""){
                $filePath = $this->baseurl.$videos['name'][$i];
                $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                if (in_array($ext, $allowed_vid)) {
                    $uploaded_videos[$i]["path"] = basename($filePath);
                    $uploaded_videos[$i]["description"] = htmlspecialchars($_POST["videos_descriptions"][$i]);
                    $uploaded_videos[$i]["type"] = "video";
                    move_uploaded_file($tmpFilePath, $filePath);
                }
            }
        }
        $this->writeToDB($uploaded_videos);
        return true;
    }

    private function writeToDB($items) {
        $stmt = $this->db->prepare("INSERT INTO gallery(`name`, schoolid, schoolyear, `description`, type) VALUES (?, ?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->bind_param("sisss", $item["path"], $this->profileinfo["schoolid"], $this->profileinfo["year"], $item["description"], $item["type"]);
            $stmt->execute();
        }
    }

    public function getNewGallery() {
        $gallery = [];
        $stmt = $this->db->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
        $stmt->bind_param("is", $this->profileinfo["schoolid"], $this->profileinfo["year"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $gallery[] = [
                "id" => $row["id"],
                "name" => $row["name"],
                "description" => $row["description"],
                "type" => $row["type"]
            ];
        }
        $stmt->close();
        return $gallery;
    }
    
    // Overwrite gallery
    public function deleteItems() {
        $stmt = $this->db->prepare("DELETE FROM gallery WHERE schoolid=? AND schoolyear=?");
        $stmt->bind_param("is", $this->profileinfo["schoolid"], $this->profileinfo["year"]);
        $stmt->execute();
        $this->delete_files($this->baseurl);
    }
}

$auth = new Auth;
$profileinfo = $auth->isProfileLoggedin();
if (!empty($profileinfo) && $auth->isUserAdmin($profileinfo)) {
    $gallery = new UploadGallery($profileinfo);
    // Overwrite items
    if (isset($_POST["overwrite"]) && $_POST["overwrite"] == true) {
        $gallery->deleteItems();
    }
    // Create dirs
    $gallery->createDirs();
    if (isset($_FILES["photos"]["name"]) && count($_FILES["photos"]["name"]) > 0) {
        $photos = $gallery->uploadPhotos($_FILES["photos"]);
    }

    if (isset($_FILES["videos"]["name"]) && count($_FILES["videos"]["name"]) > 0) {
        $videos = $gallery->uploadVideos($_FILES["videos"]);
    }

    $newgallery = $gallery->getNewGallery();
    $response = [
        "code" => "C",
        "data" => $newgallery
    ];
}
else {
    http_response_code(401);
    $response = [
        "code" => "E",
        "error" => L::common_needToLogin
    ];
}

sendJSON($response);
?>
