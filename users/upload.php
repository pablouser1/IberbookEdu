<?php
// -- Handle user data -- //
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");
require_once("../config/config.php");

class Upload {
    private $db;
    private $userinfo;
    private $profileinfo;
    private $baseurl;

    function __construct($userinfo, $profileinfo) {
        $this->db = new DB;
        $this->userinfo = $userinfo;
        $this->profileinfo = $profileinfo;
        $this->baseurl = $GLOBALS["uploadpath"].$this->profileinfo["schoolid"]."/".$this->profileinfo["year"]."/".$this->userinfo["type"]."/".$this->profileinfo["id"]."/";
    }

    public function startUpload($files) {
        $result = [];
        // Create necessary dirs first
        $this->createDirs();

        // Get not uploaded elements
        $remain = $this->getNotUploaded();

        // -- Continue with elements not uploaded yet

        // Photo
        if (isset($files['photo'])) {
            // Not in remaining array, replacing
            if (!in_array("photo", $remain)) {
                $this->deleteMedia("photo");
            }
            $result["photo"] = $this->uploadPhoto($files["photo"]);
        }

        // Video
        if (isset($files['video'])) {
            if (!in_array("video", $remain)) {
                $this->deleteMedia("video");
            }
            $result["video"] = $this->uploadVideo($files["video"]);
        }

        // Quote
        if (isset($_POST['quote']) && !empty($_POST["quote"])) {
            $result["quote"] = $this->uploadQuote($_POST["quote"]);
        }

        // Link
        if (isset($_POST['link']) && !empty($_POST["link"])) {
            $result["link"] = $this->uploadLink($_POST["link"]);
        }
        return $result;
    }

    private function uploadPhoto($photo) {
        $allowed_pic = array('gif', 'png', 'jpg', 'jpeg');
        $tmpFilePath = $photo['tmp_name'];
        if($tmpFilePath != ""){
            $picPath = $this->baseurl.$photo['name'];
            $ext = pathinfo($picPath, PATHINFO_EXTENSION);
            // If the extension is not in the array create error message
            if (in_array($ext, $allowed_pic)) {
                $picname = basename($picPath);
                move_uploaded_file($tmpFilePath, $picPath);
                $stmt = $this->db->prepare("UPDATE profiles SET photo = ? WHERE id=?");
                $stmt->bind_param("ss", $photo["name"], $this->profileinfo["id"]);
                $stmt->execute();
                $stmt->close();
                return $photo["name"];
            }
        }
        return false;
    }

    private function uploadVideo($video) {
        $allowed_vid = array('mp4', 'webm');
        $tmpFilePath = $video['tmp_name'];
        if($tmpFilePath != ""){
            $vidPath = $this->baseurl.$video['name'];
            $ext = pathinfo($vidPath, PATHINFO_EXTENSION);
            // If the extension is not in the array create error message
            if (in_array($ext, $allowed_vid)) {
                $vidname = basename($vidPath);
                move_uploaded_file($tmpFilePath, $vidPath);
                $stmt = $this->db->prepare("UPDATE profiles SET video = ? WHERE id=?");
                $stmt->bind_param("ss", $video["name"], $this->profileinfo["id"]);
                $stmt->execute();
                $stmt->close();
                return $video["name"];
            }
        }
        return false;
    }

    private function uploadQuote($quote) {
        // TODO, SET MAXIMUM CHARS
        if (strlen($quote) > 200) {
            return false;
        }
        else {
            $sanitizedQuote = nl2br(htmlspecialchars($quote));
            $stmt = $this->db->prepare("UPDATE profiles SET quote = ? WHERE id=?");
            $stmt->bind_param("ss", $sanitizedQuote, $this->profileinfo["id"]);
            $stmt->execute();
            $stmt->close();
            return $sanitizedQuote;
        }
    }

    private function uploadLink($link) {
        if (!filter_var($link, FILTER_VALIDATE_URL)) {
            return false;
        }
        else {
            $stmt = $this->db->prepare("UPDATE profiles SET link = ? WHERE id=?");
            $stmt->bind_param("ss", $link, $this->profileinfo["id"]);
            $stmt->execute();
            $stmt->close();
            return $link;
        }
    }

    private function createDirs() {
        if (!is_dir($this->baseurl)){
            mkdir($this->baseurl, 0755, true);
        }
    }

    private function deleteMedia($element) {
        $stmt = $this->db->prepare("SELECT $element from profiles where id=?");
        $stmt->bind_param("i", $this->profileinfo["id"]);
        $stmt->execute();
        $stmt->bind_result($name);
        $stmt->fetch();

        $stmt->close();
        $stmt = $this->db->prepare("UPDATE profiles SET $element = NULL WHERE id=?");
        $stmt->bind_param("i", $this->profileinfo["id"]);
        $stmt->execute();
        // Delete from file system
        unlink($this->baseurl.$name);
    }

    // Get elements not uploaded yet
    private function getNotUploaded() {
        $remain = [];
        $stmt = $this->db->prepare("SELECT photo, video, link, quote FROM profiles WHERE id=?");
        $stmt->bind_param("i", $this->profileinfo["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1){
            while ($row = mysqli_fetch_assoc($result)) {
                foreach ($row as $field => $value) {
                    if (!$value) {
                        array_push($remain, $field);
                    }
                }
            }
        }
        return $remain;
    }
}

$auth = new Auth;
$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo && $_SERVER["REQUEST_METHOD"] === "POST") {
    $upload = new Upload($userinfo, $profileinfo);
    $uploadResult = $upload->startUpload($_FILES);
    if ($uploadResult) {
        $response = [
            "code" => "C",
            "data" => $uploadResult
        ];
        sendJSON($response);
    }
}
?>
