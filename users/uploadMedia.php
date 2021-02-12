<?php
// -- Handle user data -- //
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");
require_once("../config/config.php");
require_once("../helpers/chunk.php");

class UploadMedia {
  private $db;
  private $userinfo;
  private $profileinfo;
  private $baseurl;
  private $type;
  public $chunk;

  function __construct($profileinfo) {
    $this->db = new DB;
    $this->chunk = new Chunk;
    $this->profileinfo = $profileinfo;
    $this->baseurl = $GLOBALS["uploadpath"].$this->profileinfo["schoolid"]."/".$this->profileinfo["year"]."/users/".$this->profileinfo["id"]."/";
  }

  public function startUpload($files) {
    $result = false;
    // Create necessary dirs first
    $this->createDirs();

    // Get not uploaded elements
    $remain = $this->getNotUploaded();

    // Photo
    if (isset($files['photo'])) {
      $this->type = "photo";
      // Not in remaining array, replacing
      if (!in_array("photo", $remain)) {
        $this->deleteMedia("photo");
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
      if (!in_array("video", $remain)) {
        $this->deleteMedia("video");
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
    if (!is_dir($this->baseurl)){
      mkdir($this->baseurl, 0755, true);
    }
  }

  public function setToDB($filename) {
    $type = $this->type;
    $stmt = $this->db->prepare("UPDATE profiles SET $type = ? WHERE id=?");
    $stmt->bind_param("si", $filename, $this->profileinfo["id"]);
    $stmt->execute();
    $stmt->close();
  }
  
  // Get elements not uploaded yet
  private function getNotUploaded() {
    $remain = [];
    $stmt = $this->db->prepare("SELECT photo, video FROM profiles WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $this->profileinfo["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1){
      $user = mysqli_fetch_assoc($result);
      foreach ($user as $field => $value) {
        if (!$value) {
          array_push($remain, $field);
        }
      }
    }
    return $remain;
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
}

$auth = new Auth;
$profileinfo = $auth->isProfileLoggedin();
if ($profileinfo && $_SERVER["REQUEST_METHOD"] === "POST") {
  $upload = new UploadMedia($profileinfo);
  $result = $upload->startUpload($_FILES);
  if ($result) {
    if ($upload->chunk->hasAllChunks()) {
      $upload->chunk->merge();
      $upload->setToDB($result);
      $response = [
        "code" => "C"
      ];
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
      "error" => "Error uploading file"
    ];
  }
  Utils::sendJSON($response);
}
?>
