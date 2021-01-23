<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");
$db = new DB;
$auth = new Auth;

class GroupInfo {
    private $conn;
    private $auth;
    private $profileinfo;
    function __construct($profileinfo) {
        $this->db = new DB;
        $this->auth = new Auth;
        $this->profileinfo = $profileinfo;
    }

    // Get only available users, used for non-admins
    public function getBasicInfo() {
        $users = [];
        $stmt = $this->db->prepare("SELECT userid, uploaded, `subject` FROM profiles WHERE schoolid=? AND schoolyear=?");

        $stmt->bind_param("ss", $this->profileinfo["schoolid"], $this->profileinfo["year"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $user = $this->getUser($row["userid"]);
            $users[] = [
                "name" => $user["name"],
                "type" => $user["type"],
                "subject" => $row["subject"],
                "uploaded" => $row["uploaded"]
            ];
        }
        $stmt->close();
        return $users;
    }

    // Get full info, with uploads. Used for admins
    public function getFullInfo() {
        $users = [];
        $stmt = $this->db->prepare("SELECT id, userid, photo, video, link, quote, uploaded, `subject`
                                    FROM profiles WHERE schoolid=? AND schoolyear=?");

        $stmt->bind_param("is", $this->profileinfo["schoolid"], $this->profileinfo["year"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $user = $this->getUser($row["userid"]);
            $users[] = [
                "id" => $row["id"],
                "name" => $user["name"],
                "type" => $user["type"],
                "photo" => $row["photo"],
                "video" => $row["video"],
                "link" => $row["link"],
                "quote" => $row["quote"],
                "uploaded" => $row["uploaded"],
                "subject" => $row["subject"]
            ];
        }
        $stmt->close();
        return $users;
    }

    private function getUser($userid) {
        $stmt = $this->db->prepare("SELECT id, fullname, `type`
                                    FROM users WHERE id=? LIMIT 1");

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
}
$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();
if ($userinfo && $profileinfo) {
    $group = new GroupInfo($profileinfo);
    if ($auth->isUserAdmin($userinfo)) {
        $users = $group->getFullInfo();
    }
    else {
        $users = $group->getBasicInfo();
    }
    $response = [
        "code" => "C",
        "data" => $users
    ];
    sendJSON($response);
}
?>
