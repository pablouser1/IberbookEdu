<?php
// -- Get user info from database -- //
require_once("db.php");
class DBInfo {
    private $userinfo = array();
    private $conn;
    function __construct($data) {
        $this->userinfo = $data;
        $this->conn = $GLOBALS["conn"];
    }

    function teachers() {
        // Teachers
        $stmt = $this->conn->prepare("SELECT id, fullname, photo, video, link, quote, uploaded, subject FROM teachers WHERE schoolid=? AND schoolyear=?");
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

    function students() {
        // Students
        $stmt = $this->conn->prepare("SELECT id, fullname, photo, video, link, quote, uploaded FROM students where schoolid=? and schoolyear=?");
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

    function gallery() {
        // Gallery
        $stmt = $this->conn->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
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
}
?>
