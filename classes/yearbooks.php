<?php
require_once(__DIR__."/../helpers/db.php");
class Yearbooks {
    private $db;
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
    public function getUserYearbook($schoolid, $year) {
        $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
        $stmt = $this->db->prepare("SELECT id, schoolid, schoolname, schoolyear, acyear, banner, votes, `generated` FROM yearbooks WHERE schoolid=? AND schoolyear=? AND acyear=? LIMIT 1");
        $stmt->bind_param("iss", $schoolid, $year, $acyear);
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
        // Sorting method
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

    public function getRandom() {
        $sql = "SELECT id, schoolid, schoolname, schoolyear, acyear, banner FROM yearbooks ORDER BY RAND() LIMIT 1";
        $result = $this->db->query($sql);
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $random = [
                "id" => $row["id"],
                "schoolname" => $row["schoolname"],
                "schoolyear" => $row["schoolyear"],
                "acyear" => $row["acyear"],
                "url" => "/yearbooks/".$row["id"]."/assets/".$row["banner"]
            ];
            return $random;
        }
        else {
            return false;
        }
    }
}
?>
