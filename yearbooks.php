<?php
require_once("functions.php");
require_once("headers.php");

require_once("auth.php");
require_once("helpers/db.php");
require_once("config/config.php");

require_once("lang/lang.php");

class Yearbooks {
    private $conn;
    function __construct() {
        $this->db = new DB;
    }

    // Get specific yearbook
    public function getOne($id) {
        $stmt = $this->db->prepare("SELECT id, schoolid, schoolname, schoolyear, acyear, banner, votes, `generated` FROM yearbooks WHERE id=? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $selectedyb = [
                "id" => (int)$row["id"],
                "schoolid" => (int)$row["schoolid"],
                "schoolname" => $row["schoolname"],
                "schoolyear" => $row["schoolyear"],
                "acyear" => $row["acyear"],
                "url" => "/yearbooks/".$row["id"],
                "banner" => $row["id"]."/assets/".$row["banner"],
                "votes" => (int)$row["votes"],
                "generated" => $row["generated"]
            ];
            return $selectedyb;
        }
        else {
            return false;
        }
    }

    // Get yearbook from logged in user
    public function getUserYearbook($userinfo) {
        $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
        $stmt = $this->db->prepare("SELECT id, schoolid, schoolname, schoolyear, acyear, banner, votes, `generated` FROM yearbooks WHERE schoolid=? AND schoolyear=? AND acyear=? LIMIT 1");
        $stmt->bind_param("iss", $userinfo["schoolid"], $userinfo["year"], $acyear);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $selectedyb = [
                "id" => (int)$row["id"],
                "schoolid" => $row["schoolid"],
                "schoolname" => $row["schoolname"],
                "schoolyear" => $row["schoolyear"],
                "acyear" => $row["acyear"],
                "banner" => $row["id"]."/assets/".$row["banner"],
                "votes" => (int)$row["votes"],
                "generated" => $row["generated"]
            ];
            return $selectedyb;
        }
        else {
            return false;
        }

    }
    // Get multiple yearbooks
    public function getYearbooks($offset, $sort) {
        $yearbooks = [];
        $sql = "SELECT id, schoolid, schoolname, schoolyear, acyear, banner, votes FROM yearbooks ORDER BY $sort DESC LIMIT 10 OFFSET $offset";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $yearbooks[] = [
                    "id" => $row["id"],
                    "schoolid" => $row["schoolid"],
                    "schoolname" => $row["schoolname"],
                    "schoolyear" => $row["schoolyear"],
                    "acyear" => $row["acyear"],
                    "banner" => $row["id"]."/assets/".$row["banner"],
                    "votes" => (int)$row["votes"]
                ];
            }
            return $yearbooks;
        }
        else {
            return false;
        }
    }

    public function sanitizeInput($offset, $sort) {
        // Offset
        if (!is_numeric($_GET["offset"])) {
            return false;
        }
        switch ($sort){
            case "votes":
            case "schoolyear":
            case "schoolname":
            case "acyear":
                return true;
                break;
            default:
            return false;
        }
    }
}

$auth = new Auth;
$yearbook = new Yearbooks;

// Loggedin user's yearbook
if (isset($_GET["mode"])) {
    if ($userinfo = $auth->isUserLoggedin()) {
        $useryb = $yearbook->getUserYearbook($userinfo);
        if ($useryb) {
            $response = [
                "code" => "C",
                "data" => $useryb
            ];
        }
        else {
            $response = [
                "code" => "C",
                "data" => null
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
}
elseif (isset($_GET["id"])) {
    $individual = $yearbook->getOne($_GET["id"]);
    $response = [
        "code" => "C",
        "data" => $individual
    ];
}
// Multiple yearbooks
else {
    $sanitized = $yearbook->sanitizeInput($_GET["offset"], $_GET["sort"]);
    if ($sanitized) {
        $yearbooks = $yearbook->getYearbooks($_GET["offset"], $_GET["sort"]);
        if ($yearbooks) {
            $response = [
                "code" => "C",
                "data" => $yearbooks
            ];
        }
        else {
            $response = [
                "code" => "NO-MORE",
                "error" => L::yearbooks_maximum
            ];
        }
    }
    else {
        $response = [
            "code" => "E",
            "error" => L::yearbooks_params
        ];
    }
}
sendJSON($response);
?>
