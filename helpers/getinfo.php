<?php
// -- Get info from database -- //
require_once("../helpers/db/db.php");
class DBInfo {
    private $userinfo = array();
    private $db;
    function __construct($data) {
        $this->userinfo = $data;
        $this->db = new DB;
    }

    // Get all teachers/students of specific group
    public function users($type) {
        $users = [];
        $stmt = $this->db->prepare("SELECT id, fullname, `type`, photo, video, link, quote, uploaded, subject
                                    FROM users WHERE schoolid=? AND schoolyear=? AND `type`=? ORDER BY fullname");

        $stmt->bind_param("sss", $this->userinfo["idcentro"], $this->userinfo["yearuser"], $type);
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

    // Get user info from database
    public function user() {
        $user = [];
        $stmt = $this->db->prepare("SELECT id, fullname, `type`, photo, video, link, quote, uploaded, subject, reason
                                    FROM users WHERE id=?");

        $stmt->bind_param("i", $this->userinfo["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $user = [
                "id" => $row["id"],
                "fullname" => $row["fullname"],
                "type" => $row["type"],
                "photo" => $row["photo"],
                "video" => $row["video"],
                "link" => $row["link"],
                "quote" => $row["quote"],
                "uploaded" => $row["uploaded"],
                "subject" => $row["subject"],
                "reason" => $row["reason"]
            ];
        }
        $stmt->close();
        return $user;
    }

    // Get all gallery
    public function gallery() {
        // Gallery
        $gallery = [];
        $stmt = $this->db->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
        $stmt->bind_param("is", $this->userinfo["idcentro"], $this->userinfo["yearuser"]);
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

    // Get yearbook info of individual user
    public function yearbook() {
        $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
        $yearbook = [];
        // Check if admin generated yearbook before
        $stmt = $this->db->prepare("SELECT generated FROM yearbooks WHERE schoolid=? AND schoolyear=? AND acyear=?");
        $stmt->bind_param("iss", $this->userinfo["idcentro"], $this->userinfo["yearuser"], $acyear);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($generated);
        if ($stmt->num_rows == 1) {
            if(($result = $stmt->fetch()) == true){
                $yearbook = [
                    "available" => true,
                    "date" => $generated,
                    "schoolid" => $this->userinfo["idcentro"],
                    "schoolyear" => $this->userinfo["yearuser"],
                    "acyear" => $acyear
                ];
            }
        }
        return $yearbook;
    }
}
?>
