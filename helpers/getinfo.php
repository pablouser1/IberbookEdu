<?php
// -- Get users info from database -- //
require_once("../helpers/db/db.php");
class DBInfo {
    private $userinfo = array();
    private $db;
    function __construct($data) {
        $this->userinfo = $data;
        $this->db = new DB;
    }

    public function teacher() {
        $teacher = [];
        // Teacher
        $stmt = $this->db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded, subject, reason
                                    FROM teachers WHERE id=?");

        $stmt->bind_param("i", $this->userinfo["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $teacher = [
                "id" => $row["id"],
                "fullname" => $row["fullname"],
                "type" => "P",
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
        return $teacher;
    }
    // Get all teachers
    public function teachers() {
        $teachers = [];
        // Teachers
        $stmt = $this->db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded, subject
                                    FROM teachers WHERE schoolid=? AND schoolyear=? ORDER BY fullname");
        
        $stmt->bind_param("is", $this->userinfo["idcentro"], $this->userinfo["yearuser"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $teachers[] = [
                "id" => $row["id"],
                "name" => $row["fullname"],
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
        return $teachers;
    }

    // Get individual student info
    public function student() {
        $student = [];
        $stmt = $this->db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded, reason
                                    FROM students WHERE id=?");
        
        $stmt->bind_param("i", $this->userinfo["id"]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1){
            $student["type"] = $this->userinfo["typeuser"];
            while ($row = $result->fetch_assoc()) {
                $student = [
                    "id" => $row["id"],
                    "fullname" => $row["fullname"],
                    "type" => "ALU",
                    "photo" => $row["photo"],
                    "video" => $row["video"],
                    "link" => $row["link"],
                    "quote" => $row["quote"],
                    "uploaded" => $row["uploaded"],
                    "reason" => $row["reason"]
                ];
            }
        }
        $stmt->close();
        return $student;
    }

    // Get all students
    public function students() {
        $students = [];
        $stmt = $this->db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded
                                    FROM students where schoolid=? and schoolyear=? ORDER BY fullname");
        
        $stmt->bind_param("is", $this->userinfo["idcentro"], $this->userinfo["yearuser"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                "id" => $row["id"],
                "name" => $row["fullname"],
                "photo" => $row["photo"],
                "video" => $row["video"],
                "link" => $row["link"],
                "quote" => $row["quote"],
                "uploaded" => $row["uploaded"]
            ];
        }
        $stmt->close();
        return $students;
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
