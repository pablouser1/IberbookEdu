<?php
require "vendor/autoload.php";
use \Firebase\JWT\JWT;
require_once("helpers/db.php");
require_once("config/config.php");
class Auth {
    private $db;
    function __construct() {
        $this->db = new DB;
    }

    public function isUserAdmin($userinfo) {
        if ($userinfo["rank"] == "admin") {
            return true;
        }
        else {
            return false;
        }
    }

    // Check if user is admin when login
    public function isUserAdminLogin($username) {
        // Check if user is admin
        $stmt = $this->db->prepare("SELECT `id`, `permissions` FROM `staff` WHERE username=? AND permissions='admin'");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            // User is admin
            return true;
        }
        else {
            // User is not admin
            return false;
        }
    }

    // Check if user sent valid cookie
    public function isUserLoggedin() {
        if (isset($_COOKIE["user"]) && !empty($_COOKIE["user"])) {
            $token = $_COOKIE["user"];
            if ($userinfo = $this->authJWT($token)) {
                return $userinfo;
            }
        }
        return false;
    }

    public function isProfileLoggedin() {
        if (isset($_COOKIE["profile"]) && !empty($_COOKIE["profile"])) {
            $token = $_COOKIE["profile"];
            if ($profileinfo = $this->authJWT($token)) {
                return $profileinfo;
            }
        }
        return false;
    }

    public function authJWT($key) {
        $secret_key = $GLOBALS["token_secret"];
        try {
            $decoded = JWT::decode($key, $secret_key, array('HS256'));
            if (isset($decoded->data->userinfo)) {
                return (array) $decoded->data->userinfo;
            }
            elseif (isset($decoded->data->profileinfo)) {
                return (array) $decoded->data->profileinfo;
            }
        }
        catch (\Firebase\JWT\SignatureInvalidException $th) {
            return false;
        }
    }

    // -- SET JWT TOKENS -- //
    public function setUserToken($userinfo) {
        $data = [
            "userinfo" => $userinfo
        ];

        $token = $this->setToken($data);

        $this->setCookie("user", $token);
    }
    
    public function setProfileToken($profileinfo) {
        $data = [
            "profileinfo" => $profileinfo
        ];

        $token = $this->setToken($data);

        $this->setCookie("profile", $token);
    }

    // -- Helpers -- //
    private function setToken($data) {
        $key = $GLOBALS["token_secret"];
        $issuedAt = time();
        $payload = array(
            "iss" => $_SERVER["HTTP_HOST"],
            "iat" => $issuedAt,
            "exp" => $issuedAt+86400,
            "nbf" => $issuedAt-5,
            "data" => $data
        );
        $jwt = JWT::encode($payload, $key);
        return $jwt;
    }

    private function setCookie($name, $token) {
        setcookie($name, $token, [
            'expires' => time()+86400,
            'httponly' => true,
            'secure' => true
        ]);
    }
}
?>
