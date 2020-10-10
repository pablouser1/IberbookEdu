<?php
// -- Get user info from database -- //
require_once("../helpers/db/db.php");
class DBInfo {
    private $userinfo = array();
    private $db;
    function __construct($data) {
        $this->userinfo = $data;
        $this->db = new DB;
    }

    public function teachers() {
        // Teachers
        $stmt = $this->db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded, subject FROM teachers WHERE schoolid=? AND schoolyear=? ORDER BY fullname");
        $stmt->bind_param("is", $this->userinfo["idcentro"], $this->userinfo["yearuser"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $teachers[] = [
                "id" => $row["id"],
                "name" => $row["fullname"],
                "photo" => $row["photo"],
                "video" => $row["video"],
                "link" => (empty($row["link"]))? "-" : $row["link"],
                "quote" => (empty($row["quote"]))? "-" : $row["quote"],
                "uploaded" => $row["uploaded"],
                "subject" => $row["subject"]
            ];
        }
        $stmt->close();
        return $teachers;
    }

    public function students() {
        // Students
        $stmt = $this->db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded FROM students where schoolid=? and schoolyear=? ORDER BY fullname");
        $stmt->bind_param("is", $this->userinfo["idcentro"], $this->userinfo["yearuser"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                "id" => $row["id"],
                "name" => $row["fullname"],
                "photo" => $row["photo"],
                "video" => $row["video"],
                "link" => (empty($row["link"]))? "-" : $row["link"],
                "quote" => (empty($row["quote"]))? "-" : $row["quote"],
                "uploaded" => $row["uploaded"]
            ];
        }
        $stmt->close();
        return $students;
    }

    public function gallery() {
        // Gallery
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

    public function yearbook() {
        $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
        $yearbook = [];
        // Check if admin generated yearbook before
        $stmt = $this->db->prepare("SELECT DATE_FORMAT(generated, '%d/%m/%Y %H:%i') FROM yearbooks WHERE schoolid=? AND schoolyear=? AND acyear=?");
        $stmt->bind_param("iss", $this->userinfo["idcentro"], $this->userinfo["yearuser"], $acyear);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($generated);
        if ($stmt->num_rows == 1) {
            if(($result = $stmt->fetch()) == true){
                $yearbook = array(
                    "available" => true,
                    "date" => $generated
                );
            }
        }
        return $yearbook;
    }
}
?>
