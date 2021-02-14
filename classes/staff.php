<?php
require_once(__DIR__."/../helpers/db.php");
class Staff {
    private $db;
    function __construct() {
        $this->db = new DB;
    }

    public function getStaff() {
        $staff = [];
        // Get all staff members
        $sql = "SELECT id, username, `permissions` FROM staff";
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
}
?>
