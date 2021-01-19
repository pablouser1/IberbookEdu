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

    function login($username, $password, $type) {
        // Prepare a select statement
        $sql = "SELECT id, password FROM users WHERE username = ?";
        if($stmt = $this->db->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            // Set parameters
            $param_username = $username;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // User logged in correctly
                            $this->type = $type;
                            $this->userid = (int)$id;
                            return [
                                "code" => "C",
                                "description" => null
                            ];
                        } else{
                            // Display an error message if password is not valid
                            $login_error = "esta contraseña no es válida.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $login_error = "no existe ninguna cuenta con este nombre de usuario.";
                }
            } else{
                $login_error = "por favor inténtelo más tarde";
            }
            // Close statement
            $stmt->close();
        }
        if ($login_error) {
            return [
                "code" => "E",
                "description" => "Ha habido un error, {$login_error}"
            ];
        }
    }

    function getinfo() {
        $stmt = $this->db->prepare("SELECT `id`, `fullname`, `type`, `schoolid`, `schoolyear` FROM users WHERE id=?");
        $stmt->bind_param("i", $this->userid);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $schoolname = $this->getSchoolName($row["schoolid"]);
            $userinfo = [
                "id" => $row["id"],
                "name" => $row["name"],
                "type" => $row["type"],
                "schoolid" => $row["schoolid"],
                "schoolname" => $schoolname,
                "year" => $schoolyear,
                "schools" => [
                    [
                        "id" => $schoolyear,
                        "name" => $schoolname,
                        "groups" => [$schoolyear]
                    ]
                ]
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
