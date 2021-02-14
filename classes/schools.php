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
}
?>
