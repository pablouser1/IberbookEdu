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
    function __construct() {
        $this->db = new DB;
    }

    public function addUsers($users) {
        $generatedUsers = [];
        foreach ($users as $user) {
            $password = $this->random_password(10);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (username, `password`, `type`, `name`, surname, schools) VALUES(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $user["username"], $hashedPassword, $user["type"], $user["name"], $user["surname"], $user["schools"]);
            if ($stmt->execute()) {
                $user["password"] = $password;
                array_push($generatedUsers, $user);
            }
            $stmt->close();
        }
        return $generatedUsers;
    }

    public function removeUsers($users) {
        $errors = [];
        foreach ($users as $user) {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $user);
            if (!$stmt->execute()) {
                array_push($errors, $user);
            }
            $stmt->close();
        }
        if (empty($errors)) {
            return true;
        }
        return false;
    }

    public function checkCSV($csvPOST) {
        $users = [];
        $csvfile = array_map('str_getcsv', file($csvPOST["tmp_name"]));
        foreach ($csvfile as $user) {
            $users[] = [
                "username" => $user[0],
                "name" => $user[1],
                "surname" => $user[2],
                "type" => $user[3],
                "schools" => $user[4]
            ];
        }
        return $users;
    }

    public function sanitizeInput($tempUsers) {
        $users = [];
        foreach ($tempUsers as $tempUser) {
            $schools = [
                [
                    "id" => (int)$tempUser["schoolid"],
                    "groups" => [
                        [
                            "name" => $tempUser["schoolyear"]
                        ]
                    ]
                ]
            ];
            $schoolsString = json_encode($schools);
            $users[] = [
                "username" => $tempUser["username"],
                "name" => $tempUser["name"],
                "surname" => $tempUser["surname"],
                "type" => $tempUser["type"],
                "schools" => $schoolsString
            ];
        }
        return $users;
    }

    /**
     * A PHP function that will generate a secure random password.
     * 
     * @param int $length The length that you want your random password to be.
     * @return string The random password.
     */
    private function random_password($length){
        $char = [range('A','Z'),range('a','z'),range(0,9),['*','#','@','!','?','.']];
        $pw = '';
        for($a = 0; $a < count($char); $a++)
        {
            $randomkeys = array_rand($char[$a], 2);
            $pw .= $char[$a][$randomkeys[0]].$char[$a][$randomkeys[1]];
        }
        $userPassword = str_shuffle($pw);
        return $userPassword;
    }
}

if (isset($_GET["action"])) {
    $mngUsers = new ManageUsers;
    if (isset($_FILES["csv"])) {
        $users = $mngUsers->checkCSV($_FILES["csv"]);
    }
    elseif (isset($_POST["users"])) {
        if ($_GET["action"] === "add") {
            $users = $mngUsers->sanitizeInput($_POST["users"]);
        }
        else {
            $users = $_POST["users"];
        }
    }
    else {
        $response = [
            "code" => "E",
            "error" => "Need more info"
        ];
    }

    if (isset($users)) {
        switch ($_GET["action"]) {
            case "add":
            case "csv":
                $generatedUsers = $mngUsers->addUsers($users);
                $response = [
                    "code" => "C",
                    "data" => $generatedUsers
                ];
                break;
            case "remove":
                $response = $mngUsers->removeUsers($users);
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
    }
    Utils::sendJSON($response);
}
?>
