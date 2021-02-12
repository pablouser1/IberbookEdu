<?php
// -- Handle user data -- //
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");

class UploadMisc {
    private $db;
    private $userinfo;
    private $profileinfo;
    private $baseurl;

    function __construct($userinfo, $profileinfo) {
        $this->db = new DB;
        $this->userinfo = $userinfo;
        $this->profileinfo = $profileinfo;
    }

    public function startUpload($files) {
        $result = [];
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

    private function uploadQuote($quote) {
        // TODO, SET MAXIMUM CHARS
        if (strlen($quote) > 60) {
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
}

$auth = new Auth;
$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo && $_SERVER["REQUEST_METHOD"] === "POST") {
    $upload = new UploadMisc($userinfo, $profileinfo);
    $uploadResult = $upload->startUpload($_FILES);
    if ($uploadResult) {
        $response = [
            "code" => "C"
        ];
        Utils::sendJSON($response);
    }
}
?>
