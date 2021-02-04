<?php
require_once(__DIR__."/../helpers/db.php");
require_once(__DIR__."/../auth.php");
require_once(__DIR__."/../config/config.php");
class Profiles {
    private $db;
    private $auth;
    function __construct() {
        $this->db = new DB;
        $this->auth = new Auth;
    }
    
    public function getProfile($userid) {
        $stmt = $this->db->prepare("SELECT id, userid, schoolid, schoolyear, photo, video, link, quote, uploaded FROM profiles WHERE `id`=?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            return $user;
        }
        else {
            return false;
        }
    }

    /**
     * changeProfile
     *
     * @param array school id and name
     * @param array group name and subject (if any)
     * @return array new profile
     */
    public function changeProfile($userid, $school, $group) {
        $stmt = $this->db->prepare("SELECT id FROM profiles WHERE userid=? AND schoolid=? AND schoolyear=?");
        $stmt->bind_param("iis", $userid, $school["id"], $group["name"]);
        $stmt->execute();
        $stmt->store_result();
        // Get profile id
        $stmt->bind_result($profileid);
        $stmt->fetch();
        $exists = $stmt->num_rows;
        $stmt->close();
        if (!$exists) {
            $profileid = $this->createProfile($userid, $school["id"], $group);
            if (!$profileid) {
                return false;
            }
        }
        $profile = [
            "id" => $profileid,
            "schoolid" => $school["id"],
            "schoolname" => $school["name"],
            "year" => $group["name"]
        ];
        $this->auth->setProfileToken($profile);
        return $profile;
    }
    
    /**
     * createProfile
     *
     * @param int $userid
     * @param int $schoolid
     * @param array $group
     * @return int Profile ID
     */
    public function createProfile($userid, $schoolid, $group) {
        $subject = null;
        if (isset($group["subject"])) {
            $subject = $group["subject"];
        }
        $stmt = $this->db->prepare("INSERT INTO profiles(userid, schoolid, schoolyear, `subject`) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("iiss", $userid, $schoolid, $group["name"], $subject);
        $stmt->execute();
        $profileid = $stmt->insert_id;
        return $profileid;
    }
    
    /**
     * streamMedia
     *
     * @param int Schoolid
     * @param string Scool year
     * @param int User id
     * @param string File name
     */
    public function streamMedia($schoolid, $year, $mediaid, $medianame) {
        $filepath = $GLOBALS["uploadpath"].$schoolid."/".$year."/users/{$mediaid}/{$medianame}";
        if(file_exists($filepath)){
            // https://www.sitepoint.com/community/t/loading-html5-video-with-php-chunks-or-not/350957
            $fp = @fopen($filepath, 'rb');
            $size = filesize($filepath); // File size
            $length = $size; // Content length
            $start = 0; // Start byte
            $end = $size - 1; // End byte
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            //header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            //header("Cache-Control: post-check=0, pre-check=0", false);
            //header("Pragma: no-cache");
            header('Content-Type: ' . finfo_file($finfo, $filepath));
            finfo_close($finfo);
            header('Content-Disposition: inline; filename="'.basename($filepath).'"');
            //header("Accept-Ranges: 0-$length");
            header("Accept-Ranges: bytes");
            if (isset($_SERVER['HTTP_RANGE'])){
                $c_start = $start;
                $c_end = $end;
                list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
                if (strpos($range, ',') !== false)
                {
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    header("Content-Range: bytes $start-$end/$size");
                    exit;
                }
                if ($range == '-')
                {
                    $c_start = $size - substr($range, 1);
                }
                else
                {
                    $range = explode('-', $range);
                    $c_start = $range[0];
                    $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
                }
                $c_end = ($c_end > $end) ? $end : $c_end;
                if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size)
                {
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    header("Content-Range: bytes $start-$end/$size");
                    exit;
                }
                $start = $c_start;
                $end = $c_end;
                $length = $end - $start + 1;
                fseek($fp, $start);
                header('HTTP/1.1 206 Partial Content');
            }
            header("Content-Range: bytes $start-$end/$size");
            header("Content-Length: " . $length);
            $buffer = 1024 * 8;
            $s = 0;
            while (!feof($fp) && ($p = ftell($fp)) <= $end){
                if ($p + $buffer > $end){
                    $buffer = $end - $p + 1;
                }
                $s = $s + 1;
                //take a break start/my modification
                echo fread($fp, $buffer);
                if ($s >= 500){
                    ob_clean();
                    ob_flush();
                    flush();
                    break;
                    //take a break
                }
                else{
                    flush();
                }
            }
            fclose($fp);
            exit();
        }
    }

    public function deleteProfileItems($id, $elements, $schoolid, $year) {
        $allowed_elements = ["video", "photo", "link", "quote"];
        $deleted_elements = 0;
        foreach ($elements as $element) {
            if (in_array($element, $allowed_elements)) {
                $profile = $this->getProfile($id);
                if ($profile) {
                    $stmt = $this->db->prepare("UPDATE profiles SET $element = NULL WHERE `id`=? AND schoolid=? AND schoolyear=?");
                    $stmt->bind_param("iis", $id, $schoolid, $year);
                    if ($stmt->execute()) {
                        // Files
                        if ($element == "photo" || $element == "video") {
                            $file = $GLOBALS["uploadpath"].$schoolid."/".$year."/users/".$id."/".$profile[$element];
                            unlink($file);
                        }
                        $deleted_elements++;
                    }
                }
            }
        }
        if (count($elements) === $deleted_elements) {
            return true;
        }
        else {
            return false;
        }
    }
}
?>
