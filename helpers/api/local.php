<?php
// TODO, add support for teachers
require_once(__DIR__. "/../../config/config.php");
require_once(__DIR__. "/../db.php");
class Api {
    private $db;
    private $type;
    private $userid;

    function __construct() {
        $this->db = new DB;
    }

    public function login($username, $password, $type) {
        // Prepare a select statement
        $stmt = $this->db->prepare("SELECT id, `password` FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($userid, $hashed_password);
                $stmt->fetch();
                if (password_verify($password, $hashed_password)) {
                    // User logged in correctly
                    $this->userid = $userid;
                    $this->type = $type;
                    $response = [
                        "code" => "C"
                    ];
                }
                else {
                    // Display an error message if password is not valid
                    $response = [
                        "code" => "E",
                        "error" => "Invalid password"
                    ];
                }
            }
            else {
                $response = [
                    "code" => "E",
                    "error" => "That username doesn't exist"
                ];
            }
        }
        else {
            $response = [
                "code" => "E",
                "error" => "There was an error processing your request, try again later"
            ];
        }
        $stmt->close();
        return $response;
    }

    public function getinfo() {
        $stmt = $this->db->prepare("SELECT `id`, `fullname`, `type`, `schools` FROM users WHERE id=?");
        $stmt->bind_param("i", $this->userid);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            // Get user's schools and groups
            $schools = json_decode($row["schools"], true);
            foreach ($schools as $i => $school) {
                $schoolname = $this->getSchoolName($school["id"]);
                $schools[$i]["name"] = $schoolname;
            }
            $userinfo = [
                "id" => $row["id"],
                "name" => $row["fullname"],
                "type" => $row["type"],
                "schools" => $schools
            ];
        }
        $stmt->close();
        return $userinfo;
    }

    private function getSchoolName($schoolid) {
        // Get all schools
        $stmt = $this->db->prepare("SELECT `name` FROM schools WHERE id=?");
        $stmt->bind_param("i", $schoolid);
        $stmt->execute();
        $stmt->store_result();
        // Get profile id
        $stmt->bind_result($schoolname);
        $stmt->fetch();
        $stmt->close();
        return $schoolname;
    }
}

?>
