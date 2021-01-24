<?php
require_once(__DIR__."/../helpers/db.php");
require_once(__DIR__."/users.php");
class Groups {
    private $db;
    private $usersClass;
    function __construct() {
        $this->db = new DB;
        $this->usersClass = new Users;
    }

    public function getProfilesGroupBasic($schoolid, $year) {
        $users = [];
        $stmt = $this->db->prepare("SELECT uploaded, `subject`
                                    FROM profiles WHERE schoolid=? AND schoolyear=?");

        $stmt->bind_param("is", $schoolid, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $user = $this->usersClass->getUser($row["userid"]);
            $users[] = [
                "name" => $user["name"],
                "type" => $user["type"],
                "uploaded" => $row["uploaded"],
                "subject" => $row["subject"]
            ];
        }
        $stmt->close();
        return $users;
    }
    
    /**
     * Return all profiles from specific school and group
     *
     * @param int $schoolid
     * @param string $year
     * @return array
     */
    public function getProfilesGroupFull($schoolid, $year) {
        $users = [];
        $stmt = $this->db->prepare("SELECT id, userid, photo, video, link, quote, uploaded, `subject`
                                    FROM profiles WHERE schoolid=? AND schoolyear=?");

        $stmt->bind_param("is", $schoolid, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $user = $this->usersClass->getUser($row["userid"]);
            $users[] = [
                "id" => $row["id"],
                "name" => $user["name"],
                "type" => $user["type"],
                "photo" => $row["photo"],
                "video" => $row["video"],
                "link" => $row["link"],
                "quote" => $row["quote"],
                "uploaded" => $row["uploaded"],
                "subject" => $row["subject"]
            ];
        }
        $stmt->close();
        return $users;
    }

    public function getGroups() {
        $groups = [];
        // Get all groups
        $sql = "SELECT id, `name` FROM groups ORDER BY `name` ASC";
        $result = $this->db->query($sql);
        while ($row = $result->fetch_assoc()) {
            $groups[] = $row;
        }
        return $groups;
    }

    public function isAllowed() {
        $sql = "SELECT `id` FROM groups WHERE `name`=$group";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows === 1) {
            return true;
        }
        return false;
    }
}
?>
