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
    private $userinfo;
    function __construct($userinfo) {
        $this->db = new DB;
        $this->auth = new Auth;
        $this->userinfo = $userinfo;
    }

    // Get only available users, used for non-admins
    public function getBasicInfo() {
        $users = [];
        $stmt = $this->db->prepare("SELECT fullname, `type`, uploaded, subject
                                    FROM users WHERE schoolid=? AND schoolyear=? ORDER BY fullname");

        $stmt->bind_param("ss", $this->userinfo["schoolid"], $this->userinfo["year"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                "name" => $row["fullname"],
                "type" => $row["type"],
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
        $stmt = $this->db->prepare("SELECT id, fullname, `type`, photo, video, link, quote, uploaded, subject
                                    FROM users WHERE schoolid=? AND schoolyear=? ORDER BY fullname");

        $stmt->bind_param("ss", $this->userinfo["schoolid"], $this->userinfo["year"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                "id" => $row["id"],
                "name" => $row["fullname"],
                "type" => $row["type"],
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
}
if ($userinfo = $auth->isUserLoggedin()) {
    $group = new GroupInfo($userinfo);
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
