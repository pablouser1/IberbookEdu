<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../../functions.php");
require_once("../../headers.php");
require_once("../../helpers/db.php");

class ManageUsers {
    private $db;
    private $pdf;
    function __construct() {
        $this->db = new DB;
    }

    public function addUsers($users) {
        foreach ($users as $user) {
            $password = password_hash($this->random_password(8), PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (username, `password`, `type`, fullname, schoolid, schoolyear) VALUES(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssis", $user["username"], $password, $user["type"], $user["fullname"], $user["schoolid"], $user["schoolyear"]);
            $stmt->execute();
            $stmt->close();
            $user["password"] = $password;
        }
        $response = [
            "code" => "C"
        ];
        return $response;
    }

    public function removeUsers($users) {
        $errors = [];
        foreach ($users as $user) {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $user["id"]);
            if (!$stmt->execute()) {
                array_push($errors, $user["id"]);
            }
            $stmt->close();
        }
    }

    public function checkCSV($csv) {
        $csvfile = array_map('str_getcsv', file($csv));
        array_walk($csvfile, function(&$a) use ($csvfile) {
          $a = array_combine($csvfile[0], $a);
        });
        array_shift($csvfile);
        $users = sanitizeInput($csvfile);
        return $users;
    }
    
    /**
     * A PHP function that will generate a secure random password.
     * 
     * @param int $length The length that you want your random password to be.
     * @return string The random password.
     */
    private function random_password($length){
        //A list of characters that can be used in our
        //random password.
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!-.[]?*()';
        //Create a blank string.
        $password = '';
        //Get the index of the last character in our $characters string.
        $characterListLength = mb_strlen($characters, '8bit') - 1;
        //Loop from 1 to the $length that was specified.
        foreach(range(1, $length) as $i){
            $password .= $characters[random_int(0, $characterListLength)];
        }
        return $password;
    }
}

if (isset($_GET["action"])) {
    if (isset($_FILES["csv"])) {
        $users = $mngUsers->checkCSV($_FILES["csv"]);
    }
    else {
        $users = $_POST["users"];
    }
    $mngUsers = new ManageUsers;
    switch ($_GET["action"]) {
        case "add":
            $response = $mngUsers->addUsers($users);
            break;
        case "remove":
            $response = $mngUsers->removeUsers($users);
            break;
        case "csv":
            $response = $mngUsers->addUsers($users);
            break;
        default:
            $response = [
                "code" => "E",
                "error" => "Not a valid action"
            ];
    }
    if (!isset($response) || empty($response)) {
        $response = [
            "code" => "E",
            "error" => "Unknown error, please try again later"
        ];
    }
    sendJSON($response);
}
?>
