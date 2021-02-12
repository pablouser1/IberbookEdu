<?php
require_once(__DIR__."/../helpers/db.php");
require_once(__DIR__."/../config/config.php");
class Gallery {
    private $conn;
    function __construct() {
        $this->db = new DB;
    }

    public function getItems($schoolid, $year) {
        $gallery = [];
        $stmt = $this->db->prepare("SELECT id, name, description, type FROM gallery where schoolid=? and schoolyear=?");
        $stmt->bind_param("is", $schoolid, $year);
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

    public function getItem($id, $schoolid, $year) {
        $stmt = $this->db->prepare("SELECT id, `name` FROM gallery WHERE id=? AND schoolid=? AND schoolyear=? LIMIT 1");
        $stmt->bind_param("iis", $id, $schoolid, $year);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($mediaid, $medianame);
        $stmt->fetch();
        if ($stmt->num_rows === 1) {
            return [
                "id" => $mediaid,
                "name" => $medianame
            ];
        }
        else {
            return false;
        }
    }
    public function streamItem($id, $schoolid, $year) {
        $gallery = $this->getItem($id, $schoolid, $year);
        if ($gallery) {
            $filepath = $GLOBALS["uploadpath"].$schoolid."/".$year."/gallery/".$gallery["name"];
            // https://stackoverflow.com/a/27805443 and https://stackoverflow.com/a/23447332
            if(file_exists($filepath)){
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                header('Content-Type: ' . finfo_file($finfo, $filepath));
                finfo_close($finfo);
                header('Content-Disposition: inline; filename="'.basename($filepath).'"');
                header('Content-Length: ' . filesize($filepath));
                readfile($filepath);
                exit;
            }
        }
        else {
            return false;
        }
    }
}
?>
