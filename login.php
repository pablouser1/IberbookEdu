<?php
require "vendor/autoload.php";
use \Firebase\JWT\JWT;
require_once("headers.php");
require_once("functions.php");
require_once("auth.php");
require_once("helpers/db.php");
require_once("config/config.php");
require_once("helpers/api.php");

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

            // Guardians aren't registered on DB and can't be admin
            if ($type !== "guardians") {
                if ($GLOBALS["login"] !== "local") {
                    $id = $this->auth->doesUserExists($this->userinfo);
                }
                else {
                    $id = $this->userinfo["id"];
                }
                $this->userinfo["id"] = $id;
                if ($this->auth->isUserAdminLogin($username)) {
                    $this->userinfo["rank"] = "admin";
                }
                else {
                    $this->userinfo["rank"] = "user";
                }
            }
            $this->auth->setToken($this->userinfo);
            return [
                "code" => "C",
                "data" => [
                    "userinfo" => $this->userinfo
                ]
            ];
        }
        else {
            return $loginres;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!$_POST["username"]){
        $login_error[] = "No has escrito ningún nombre de usuario.";
    }
    else{
        $username = trim($_POST["username"]);
    }
    // Check if password is empty
    if(!$_POST["password"]){
        $login_error[] = "No has escrito ninguna contraseña.";
    } 
    else{
        $password = trim($_POST["password"]);
    }
    
    // Get type
    if(!$_POST["type"]){
        $login_error[] = "No has seleccionado tu tipo.";
    } 
    else{
        $type = trim($_POST["type"]);
    }
    $login = new Login;
    $loginoutput = $login->loginUser($username, $password, $type);
    sendJSON($loginoutput);
}
?>
