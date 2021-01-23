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
            $response = [
                "code" => "",
                "data" => []
            ];
            // Get user info
            $this->userinfo = $this->api->getinfo();
            if ($this->auth->isUserAdminLogin($username)) {
                $this->userinfo["rank"] = "admin";
            }
            else {
                $this->userinfo["rank"] = "user";
            }
            $response["data"]["userinfo"] = $this->userinfo;
            $this->auth->setUserToken($this->userinfo);
            $response["code"] = "C";
            return $response;
        }
        else {
            return $loginres;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!isset($_POST["username"])){
        $login_error[] = L::login_noUsername;
    }
    else{
        $username = trim($_POST["username"]);
    }
    // Check if password is empty
    if(!isset($_POST["password"])){
        $login_error[] = L::login_noPassword;
    } 
    else{
        $password = trim($_POST["password"]);
    }
    
    // Get type
    if(!isset($_POST["type"])){
        $login_error[] = L::login_noType;
    } 
    else{
        $type = trim($_POST["type"]);
    }
    $login = new Login;
    $loginoutput = $login->loginUser($username, $password, $type);
    sendJSON($loginoutput);
}
?>
