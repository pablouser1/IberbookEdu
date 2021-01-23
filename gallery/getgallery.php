<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");

$auth = new Auth;

class Gallery {
    private $conn;
    private $profileinfo;
    function __construct($profileinfo) {
        $this->db = new DB;
        $this->profileinfo = $profileinfo;
    }

    public function getItems() {
        $gallery = [];
        $stmt = $this->db->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
        $stmt->bind_param("is", $this->profileinfo["schoolid"], $this->profileinfo["year"]);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $gallery[] = [
                "id" => $row["id"],
                "name" => $row["name"],
                "description" => $row["description"],
                "type" => $row["type"]
            ];
        }
        $stmt->close();
        return $gallery;
    }
}
$profileinfo = $auth->isProfileLoggedin();
if ($profileinfo) {
    $gallery = new Gallery($profileinfo);
    $items = $gallery->getItems();

    $response = [
        "code" => "C",
        "data" => $items
    ];
    sendJSON($response);
}
?>
