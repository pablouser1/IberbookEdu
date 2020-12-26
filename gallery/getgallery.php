<?php
require_once("../headers.php");
require_once("../functions.php");
require_once("../auth.php");
require_once("../helpers/db.php");

$auth = new Auth;

class Gallery {
    private $conn;
    private $userinfo;
    function __construct($userinfo) {
        $this->db = new DB;
        $this->userinfo = $userinfo;
    }

    public function getItems() {
        $gallery = [];
        $stmt = $this->db->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
        $stmt->bind_param("is", $this->userinfo["schoolid"], $this->userinfo["year"]);
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
if ($userinfo = $auth->isUserLoggedin()) {
    $gallery = new Gallery($userinfo);
    $items = $gallery->getItems();

    $response = [
        "code" => "C",
        "data" => $items
    ];
    sendJSON($response);
}
?>
