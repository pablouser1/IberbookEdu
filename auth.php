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

    // CED Api exclusive
    public function doesUserExists($userinfo) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE idced=? AND schoolyear=? AND schoolid=?");
        $stmt->bind_param("ssi", $userinfo["idced"], $userinfo["year"], $userinfo["schoolid"]);
        $stmt->execute();
        $stmt->store_result();
        // Get profile id
        $stmt->bind_result($idprofile);
        $stmt->fetch();
        $exists = $stmt->num_rows;
        $stmt->close();
        if ($exists === 0) {
            return $this->createUser($userinfo);
        }
        else {
            return $idprofile;
        }
    }

    private function createUser($userinfo) {
        $subject = null;
        if (isset($userinfo["subject"])) {
            $subject = $userinfo["subject"];
        }
        // Create user
        $stmt = $this->db->prepare("INSERT INTO `users` (`idced`, `type`, `fullname`, `schoolid`, `schoolyear`, `subject`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $userinfo["idced"], $userinfo["type"], $userinfo["name"], $userinfo["schoolid"], $userinfo["year"], $subject);
        $stmt->execute();
        $userid = $stmt->insert_id;
        return $userid;
    }

    // Check if user is admin
    public function isUserAdmin($userinfo) {
        // Check if user is admin
        $sql = "SELECT `id`, `permissions` FROM `staff` WHERE id ='$userinfo[id]' AND permissions='admin'";
        $result = $this->db->query($sql);
        if ($result->num_rows === 1) {
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
        if (isset($_COOKIE["login"]) && !empty($_COOKIE["login"])) {
            $token = $_COOKIE["login"];
            if ($userinfo = $this->authJWT($token)) {
                return $userinfo;
            }
        }
        return false;
    }

    public function authJWT($key) {
        $secret_key = $GLOBALS["token_secret"];
        try {
            $decoded = JWT::decode($key, $secret_key, array('HS256'));
            return (array) $decoded->data->userinfo;
        } catch (\Firebase\JWT\SignatureInvalidException $th) {
            return null;
        }
    }

    // Set JWT token
    public function setToken($userinfo) {
        $key = $GLOBALS["token_secret"];
        $issuedAt = time();
        $payload = array(
            "iss" => $_SERVER["HTTP_HOST"],
            "iat" => $issuedAt,
            "data" => [
                "userinfo" => $userinfo
            ]
        );
        $jwt = JWT::encode($payload, $key);

        setcookie("login", $jwt, [
            'expires' => time()+86400,
            'httponly' => true,
            'secure' => true
        ]);
    }
}
?>
