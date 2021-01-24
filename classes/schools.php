<?php
require_once(__DIR__."/../helpers/db.php");
class Schools {
    private $db;
    function __construct() {
        $this->db = new DB;
    }
    public function getSchools() {
        $schools = [];
        // Get all schools
        $sql = "SELECT id, `name` FROM schools";
        $result = $this->db->query($sql);
        while ($row = $result->fetch_assoc()) {
            $schools[] = $row;
        }
        return $schools;
    }

    public function isAllowed($schoolid) {
        $sql = "SELECT `id` FROM `schools` WHERE id=$schoolid";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows === 1) {
            return true;
        }
        return false;
    }
}
?>
