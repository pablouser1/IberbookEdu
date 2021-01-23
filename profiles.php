<?php
require_once("headers.php");
require_once("functions.php");
require_once("auth.php");

class Profiles {
    private $db;
    private $auth;
    private $userid;
    function __construct($auth, $userid) {
        $this->db = new DB;
        $this->auth = $auth;
        $this->userid = $userid;
    }

    public function changeProfile($school, $group) {
        $stmt = $this->db->prepare("SELECT id FROM profiles WHERE userid=? AND schoolid=? AND schoolyear=?");
        $stmt->bind_param("iis", $this->userid, $school["id"], $group["name"]);
        $stmt->execute();
        $stmt->store_result();
        // Get profile id
        $stmt->bind_result($profileid);
        $stmt->fetch();
        $exists = $stmt->num_rows;
        $stmt->close();
        if (!$exists) {
            $profileid = $this->createProfile($this->userid, $school["id"], $group);
            if (!$profileid) {
                return false;
            }
        }
        $profile = [
            "id" => $profileid,
            "schoolid" => $school["id"],
            "schoolname" => $school["name"],
            "year" => $group["name"]
        ];
        $this->auth->setProfileToken($profile);
        return $profile;
    }

    private function createProfile($userid, $schoolid, $group) {
        $stmt = $this->db->prepare("INSERT INTO profiles(userid, schoolid, schoolyear) VALUES(?, ?, ?)");
        $stmt->bind_param("iis", $userid, $schoolid, $group);
        $stmt->execute();
        $profileid = $stmt->insert_id;
        return $profileid;
    }
}

// Get all profiles of user
$auth = new Auth;
if ($userinfo = $auth->isUserLoggedin()) {
    $profilesMng = new Profiles($auth, $userinfo["id"]);
    if (isset($_GET["action"])) {
        switch ($_GET["action"]) {
            case "set":
                if (isset($_POST["schoolindex"], $_POST["groupindex"])) {
                    $schoolindex = $_POST["schoolindex"];
                    $groupindex = $_POST["groupindex"];
                    $school = (array) $userinfo["schools"][$schoolindex];
                    $group = (array) $school["groups"][$groupindex];
                    if ($school && $group) {
                        $profile = $profilesMng->changeProfile($school, $group);
                    }
                    if ($profile) {
                        $response = [
                            "code" => "C",
                            "data" => [
                                "profileinfo" => $profile
                            ]
                        ];
                    }
                    else {
                        $response = [
                            "code" => "E",
                            "error" => "Unknown error while changing profile"
                        ];
                    }
                }
                else {
                    $response = [
                        "code" => "E",
                        "error" => "Not enough info provided"
                    ];
                }
                break;
            default:
                $response = [
                    "code" => "E",
                    "error" => "Action not valid"
                ];
        }
    }
}
else {
    http_response_code(401);
    $response = [
        "code" => "E",
        "error" => "Not logged in"
    ];
}
sendJSON($response);
?>
