<?php
require_once("headers.php");
require_once("functions.php");
require_once("auth.php");
require_once("helpers/db.php");
require_once("config/config.php");
require_once("lang/lang.php");

class Login {
    private $db;
    private $auth;
    private $userid;
    function __construct() {
        $this->db = new DB;
    }

    public function login($username, $password) {
        // Prepare a select statement
        $stmt = $this->db->prepare("SELECT id, `password` FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($userid, $hashed_password);
                $stmt->fetch();
                if (password_verify($password, $hashed_password)) {
                    // User logged in correctly
                    $this->userid = $userid;
                    $response = [
                        "code" => "C"
                    ];
                }
                else {
                    // Display an error message if password is not valid
                    $response = [
                        "code" => "E",
                        "error" => L::login_invalidPassword
                    ];
                }
            }
            else {
                $response = [
                    "code" => "E",
                    "error" => L::user_notExist
                ];
            }
        }
        else {
            $response = [
                "code" => "E",
                "error" => L::common_error
            ];
        }
        $stmt->close();
        return $response;
    }

    public function getInfo() {
        $stmt = $this->db->prepare("SELECT `id`, CONCAT(`name` , ' ' , surname) AS fullname, `type`, `schools` FROM users WHERE id=?");
        $stmt->bind_param("i", $this->userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        // Get user's schools and groups
        $schools = json_decode($user["schools"], true);
        foreach ($schools as $i => $school) {
            $schoolName = $this->getSchoolName($school["id"]);
            $schools[$i]["name"] = $schoolName;
        }
        $userinfo = [
            "id" => $user["id"],
            "name" => $user["fullname"],
            "type" => $user["type"],
            "schools" => $schools
        ];
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_error = [];
    if(!isset($_POST["username"]) || empty($_POST["username"])){
        $login_error[] = L::login_noUsername;
    }
    else{
        $username = trim($_POST["username"]);
    }
    // Check if password is empty
    if(!isset($_POST["password"]) || empty($_POST["password"])){
        $login_error[] = L::login_noPassword;
    } 
    else{
        $password = trim($_POST["password"]);
    }
    
    if (empty($login_error)) {
        $auth = new Auth;
        $login = new Login;
        $loginOutput = $login->login($username, $password);
        if ($loginOutput["code"] === "C") {
            $userinfo = $login->getInfo();
            if ($userinfo) {
                if ($auth->isUserAdminLogin($username)) {
                    $userinfo["rank"] = "admin";
                }
                else {
                    $userinfo["rank"] = "user";
                }
                $auth->setUserToken($userinfo);
                $response = [
                    "code" => "C",
                    "data" => [
                        "userinfo" => $userinfo
                    ]
                ];
            }
            else {
                $response = [
                    "code" => "E",
                    "error" => "Error while getting userinfo"
                ];
            }
        }
        else {
            $response = $loginOutput;
        }
    }
    else {
        $response = [
            "code" => "E",
            "error" => $login_error
        ];
    }

    Utils::sendJSON($response);
}
?>
