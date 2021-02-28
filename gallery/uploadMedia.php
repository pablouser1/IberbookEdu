<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");
require_once("../helpers/chunk.php");
require_once("../config/config.php");
require_once("../lang/lang.php");

class UploadGallery {
    private $db;
    private $profileinfo;
    private $baseurl;
    private $type;
    public $chunk;
    function __construct($profileinfo) {
        $this->db = new DB;
        $this->profileinfo = $profileinfo;
        $this->baseurl = $GLOBALS["uploadpath"].$this->profileinfo["schoolid"]."/".$this->profileinfo["year"]."/gallery/";
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
        $itemID = false;
        $stmt = $this->db->prepare("INSERT INTO gallery(`name`, schoolid, schoolyear, type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $item, $this->profileinfo["schoolid"], $this->profileinfo["year"], $this->type);
        if ($stmt->execute()) {
            $itemID = $stmt->insert_id;
        }
        $stmt->close();
        return $itemID;
    }
}

$auth = new Auth;
$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo && $auth->isUserAdmin($userinfo)) {
    $upload = new UploadGallery($profileinfo);
    $result = $upload->startUpload($_FILES);
    if ($result) {
        if ($upload->chunk->hasAllChunks()) {
            $upload->chunk->merge();
            $id = $upload->setToDB($result);
            if ($id) {
                $response = [
                    "code" => "C",
                    "id" => $id
                ];
            }
            else {
                $response = [
                    "code" => "E",
                    "error" => "Unable to write item to database"
                ];
            }
        }
        else {
            $response = [
                "code" => "MORE"
            ];
        }
    }
    else {
        $response = [
            "code" => "E",
            "error" => "Error uploading gallery"
        ];
    }
}
else {
    http_response_code(401);
    $response = [
        "code" => "E",
        "error" => L::common_needToLogin
    ];
}

Utils::sendJSON($response);
?>
