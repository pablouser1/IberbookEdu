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
                                    FROM users WHERE id=?");

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
                                    FROM users WHERE id=?");

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

    // -- Accounts auth -- //
    public function checkPassword($id, $password) {
        $stmt = $this->db->prepare("SELECT `password` FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($dbPassword);
            $stmt->fetch();
            if (password_verify($password, $dbPassword)) {
                return true;
            }
        }
        return false;
    }

    // Check if password has some requierements
    public function isPasswordValid($password) {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            return false;
        }
        return true;
    }

    public function changePassword($id, $oldPassword, $newPassword) {
        if ($this->isPasswordValid($newPassword)) {
            if ($this->checkPassword($id, $oldPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("UPDATE users SET password=? WHERE id=?");
                $stmt->bind_param("si", $hashedPassword, $id);
                if ($stmt->execute()) {
                    return true;
                }
            }
        }
        return false;
    }
}
?>
