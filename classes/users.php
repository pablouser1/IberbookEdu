<?php
require_once(__DIR__."/../helpers/db.php");
class Users {
    private $db;
    function __construct() {
        $this->db = new DB;
    }
    
    /**
     * Get user's basic info from database
     *
     * @param  int User id from users DB
     * @return array User info
     */
    public function getUser($userid) {
        $stmt = $this->db->prepare("SELECT id, `type`, CONCAT(`name` , ' ' , surname) AS fullname
                                    FROM users WHERE id=? LIMIT 1");

        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $user = [
            "id" => $row["id"],
            "name" => $row["fullname"],
            "type" => $row["type"]
        ];
        $stmt->close();
        return $user;
    }

    public function getName($userid) {
        $stmt = $this->db->prepare("SELECT CONCAT(`name` , ' ' , surname) AS fullname
                                    FROM users WHERE id=? LIMIT 1");

        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $name = $row["fullname"];
        return $name;
    }

    public function getAllUsers() {
        $users = [];
        $sql = "SELECT id, `type`, CONCAT(`name` , ' ' , surname) AS fullname FROM users";
        $result = $this->db->query($sql);
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                "id" => $row["id"],
                "name" => $row["fullname"],
                "type" => $row["type"]
            ];
        }
        return $users;
    }
}
?>
