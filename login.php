<?php
require_once("headers.php");
require_once("functions.php");
require_once("auth.php");
require_once("helpers/db.php");
require_once("config/config.php");
require_once("helpers/api.php");
require_once("lang/lang.php");

class Login {
    private $api;
    private $conn;
    private $auth;
    private $userinfo;
    function __construct() {
        $this->api = new Api;
        $this->db = new DB;
        $this->auth = new Auth;
    }

    // -- MAIN LOGINS -- //
    // General login procedure
    public function loginUser($username, $password, $type) {
        $loginres = $this->api->login($username, $password, $type);
        if ($loginres["code"] === "C"){
            // Get user info
            $this->userinfo = $this->api->getinfo();
            if ($this->userinfo) {
                if ($this->auth->isUserAdminLogin($username)) {
                    $this->userinfo["rank"] = "admin";
                }
                else {
                    $this->userinfo["rank"] = "user";
                }
                $this->auth->setUserToken($this->userinfo);
                $response = [
                    "code" => "C",
                    "data" => [
                        "userinfo" => $this->userinfo
                    ]
                ];
            }
            else {
                $response = [
                    "code" => "E",
                    "error" => "Error when getting user info"
                ];
            }
            return $response;
        }
        else {
            return $loginres;
        }
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
    
    // Get type
    if(isset($_POST["type"]) && !empty($_POST["type"])){
        switch ($_POST["type"]) {
            case "students":
            case "guardians":
            case "teachers":
                $type = $_POST["type"];
                break;
            default:
                $login_error[] = L::login_noType;
        }
    }
    else{
        $login_error[] = L::login_noType;
    }
    
    if (empty($login_error)) {
        $login = new Login;
        $loginoutput = $login->loginUser($username, $password, $type);
    }
    else {
        $loginoutput = [
            "code" => "E",
            "error" => $login_error
        ];
    }

    Utils::sendJSON($loginoutput);
}
?>
