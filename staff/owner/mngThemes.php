<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../../helpers/db.php");
require_once("../../functions.php");

// TODO handle themes
class MngThemes {
    private $db;
    function __construct() {
        $this->db = new DB;
        $this->baseurl = __DIR__."/../../themes";
    }

    public function addTheme($file) {
        $zip = new ZipArchive;
        if ($zip->open($file) && $zip->locateName('theme.json')) {
            // Valid zip, extract to folder
            $theme_json = json_decode($zip->getFromName('theme.json'), true);
            $name = $theme_json["name"];
            $dir = $this->baseurl."/{$name}";
            if (!is_dir($dir)) {
                mkdir($dir, 0750, true);
            }
            if ($zip->extractTo($dir)) {
                // If extracted, write to DB
                $stmt = $this->db->prepare("INSERT INTO themes (`name`) VALUES (?)");
                $stmt->bind_param("s", $name);
                if ($stmt->execute()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function deleteTheme($name) {
        $stmt = $this->db->prepare("DELETE FROM themes WHERE `name`=?");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            // Delete from filesystem
            $dir = $this->baseurl."/{$name}";
            Utils::recursiveRemove($dir);
        }
    }

}

if (isset($_GET["action"])) {
    $mngThemes = new MngThemes;
    switch ($_GET["action"]) {
        case "add":
            if (isset($_FILES["themeZip"])) {
                $ext = pathinfo($_FILES["themeZip"]["name"], PATHINFO_EXTENSION);
                if ($ext === "zip") {
                    $res = $mngThemes->addTheme($_FILES["themeZip"]["tmp_name"]);
                }
            }
            break;
        case "remove":
            if (isset($_POST["themeName"])) {
                $res = $mngThemes->deleteTheme($_POST["themeName"]);
            }
            break;
        default:
            die("Type a valid action");
    }
    if (isset($res) && $res) {
        die("OK");
    }
    else {
        die("Error");
    }
}

?>
