<?php
require_once(__DIR__."/../helpers/db.php");
class Themes {
    private $db;
    function __construct() {
        $this->db = new DB;
    }

    public function getThemes() {
        $themes = [];
        $sql = "SELECT `id`, `name` FROM themes";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $themeDetails = $this->getThemeDetails($row["name"]);
                $themes[] = [
                    "id" => (int) $row["id"],
                    "name" => $row["name"],
                    "description" => $themeDetails["description"],
                    "zip" => (boolean) $themeDetails["zip"]
                ];
            }
            return $themes;
        }
        else {
            return [];
        }
    }

    public function checkTheme($theme) {
        $stmt = $this->db->prepare("SELECT `id`, `name` FROM themes WHERE `id`=? LIMIT 1");
        $stmt->bind_param("i", $theme);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $themeDetails = $this->getThemeDetails($row["name"]);
            $theme = [
                "id" => (int) $row["id"],
                "name" => $row["name"],
                "description" => $themeDetails["description"],
                "zip" => (boolean) $themeDetails["zip"]
            ];
            return $theme;
        }
        else {
            return false;
        }
    }

    private function getThemeDetails($name) {
        $baseurl = __DIR__."/../themes/{$name}";
        $json_file = file_get_contents($baseurl."/theme.json");
        $themeDetails = json_decode($json_file, true);
        return $themeDetails;
    }
}
?>
