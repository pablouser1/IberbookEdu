<?php
// -- Get info from database -- //
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../../functions.php");
require_once("../../helpers/db.php");
class DBPrivInfo {
    private $db;
    function __construct() {
        $this->db = new DB;
    }

    // Get all teachers/students of specific group
    public function users($schoolid, $schoolyear) {
        $users = null;
        $stmt = $this->db->prepare("SELECT id, fullname, `type`, photo, video, link, quote, uploaded, subject
                                    FROM users WHERE schoolid=? AND schoolyear=? ORDER BY fullname");

        $stmt->bind_param("ss", $schoolid, $schoolyear);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = $row["id"];
                $users[$id] = [
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
        }
        return $users;
    }

    // Get all gallery
    public function gallery($schoolid, $schoolyear) {
        // Gallery
        $gallery = null;
        $stmt = $this->db->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
        $stmt->bind_param("is", $schoolid, $schoolyear);
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

    public function staff() {
        $staff = [];
        // Get all staff members
        $sql = "SELECT id, username, permissions FROM staff";
        $result = $this->db->query($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $staff[] = [
                "id" => $row["id"],
                "username" => $row["username"],
                "permissions" => $row["permissions"]
            ];
        }
        return $staff;
    }

    public function groups() {
        $groups = [];
        // Get all groups
        $sql = "SELECT id, `name` FROM groups ORDER BY `name` ASC";
        $result = $this->db->query($sql);
        while ($row = $result->fetch_assoc()) {
            $groups[] = $row;
        }
        return $groups;
    }

    public function schools() {
        $schools = [];
        // Get all schools
        $sql = "SELECT id, `name` FROM schools";
        $result = $this->db->query($sql);
        while ($row = $result->fetch_assoc()) {
            $schools[] = $row;
        }
        return $schools;
    }
    public function allUsers() {
        $users = [];
        $stmt = $this->db->prepare("SELECT id, fullname, `type`, schoolid, schoolyear, subject FROM users ORDER BY fullname");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            $stmt->close();
        }
        return $users;
    }
}

if (isset($_GET["schoolid"], $_GET["schoolyear"])) {
    $info = new DBPrivInfo;
    $users = $info->users($_GET["schoolid"], $_GET["schoolyear"]);
    $gallery = $info->gallery($_GET["schoolid"], $_GET["schoolyear"]);
    if (!empty($users)) {
        $response = [
            "code" => "C",
            "data" => [
                "users" => $users,
                "gallery" => $gallery
            ]
        ];
    }
    else {
        $response = [
            "code" => "E"
        ];
    }
    sendJSON($response);
}
?>
