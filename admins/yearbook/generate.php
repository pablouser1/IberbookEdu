<?php
// -- Build variables with database data, copy files to temp folder and continue with generate.php -- //

session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== "admin") {
    header("Location: ../../login.php");
    exit;
}
require_once("../../helpers/db/db.php");
require_once("../../helpers/config.php");
require_once("themes.php");
// -- Functions -- //

// Send json
function sendJSON($response) {
    header('Content-type: application/json');
    echo json_encode($response);
    exit;
}


// Separate names
function getname($type, $string){
    if($type == "abbr"){
        $nametemp = explode(",", $string);
        $surnameabbr = $nametemp[0][0].".";
        $finalname = trim($nametemp[1])." ".$surnameabbr;
        return $finalname;
    }
    elseif($type == "full"){
        return array(
            "name" => substr($string, strpos($string, ",") + 2),
            "surname" => strtok($string, ',')
        );
    }
}
// Copying files
function recursivecopy($source, $dest){
    foreach (
        $iterator = new \RecursiveIteratorIterator(
         new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
         \RecursiveIteratorIterator::SELF_FIRST) as $item
       ) {
         if ($item->isDir()) {
             mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
         } else {
           copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
         }
       }
}

if (isset($_GET["theme"]) && in_array($_GET["theme"], $themes)) {
    $theme = $_GET["theme"];
}
else {
    $response = [
        "code" => "E",
        "description" => "Esa plantilla no es válida o no has elegido ninguna"
    ];
    sendJSON($response);
}

// -- Initial vars -- //

// User info
$userinfo = $_SESSION["userinfo"];
$db = new DB;
// Used later for directories without spaces
$yearuser = str_replace(' ', '', $userinfo["yearuser"]);

// Get academic year (2020/2021 for example)
$acyear = date("Y",strtotime("-1 year"))."-".date("Y");

// Get current date (used later)
$dt = new DateTime("now", new DateTimeZone('Europe/Madrid'));
// Yearbook complete path
$baseurl = $_SERVER["DOCUMENT_ROOT"].$ybpath.$userinfo["idcentro"].'/'.$acyear."/".$yearuser;
// Create yearbook dir
if(!is_dir($baseurl)) {
    mkdir($baseurl, 0755, true);
}

// Teachers
$stmt = $db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded, subject FROM teachers WHERE schoolid=? AND schoolyear=? ORDER BY fullname");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$teachers_dir = 'teachers/';
while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $teachers[] = [
        "userid" => $id,
        "photo" => $teachers_dir.$id.'/'.$row["photo"],
        "video" => $teachers_dir.$id.'/'.$row["video"],
        "fullname" => getname("full", $row["fullname"]),
        "abbr" => getname("abbr", $row["fullname"]),
        "url" => $row["link"],
        "quote" => $row["quote"], // User quote
        "date" => $row["uploaded"],
        "zuckdate" => strtotime($row["uploaded"]),
        "subject" => $row["subject"]
    ];
}
$stmt->close();

// Students
$stmt = $db->prepare("SELECT id, fullname, photo, video, link, quote, uploaded FROM students WHERE schoolid=? AND schoolyear=? ORDER BY fullname");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$students_dir = 'students/';
while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $students[] = [
        "userid" => $id,
        "photo" => $students_dir.$id.'/'.$row["photo"],
        "video" => $students_dir.$id.'/'.$row["video"],
        "fullname" => getname("full", $row["fullname"]),
        "abbr" => getname("abbr", $row["fullname"]),
        "url" => $row["link"],
        "quote" => $row["quote"], // User quote
        "date" => $row["uploaded"],
        "zuckdate" => strtotime($row["uploaded"])
    ];
}
$stmt->close();
if ( (count($students) || count($teachers)) == 0 ) {
    $response = [
        "code" => "E",
        "description" => "Necesitas un mínimo de un alumno y un profesor para continuar"
    ];
    sendJSON($response);
}

// Gallery
$stmt = $db->prepare("SELECT name, description, type FROM gallery WHERE schoolid=? AND schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$gallery = array();
$gallery_dir = 'gallery/';
$i = 0;
while($row = mysqli_fetch_assoc($result)) {
    $gallery[$i]["path"] = $gallery_dir.$row["name"];
    $gallery[$i]["description"] = $row["description"];
    $gallery[$i]["type"] = $row["type"];
    $i++;
}
$stmt->close();

// Get school info
$schoolurl = null;
$stmt = $db->prepare("SELECT url FROM schools WHERE id=?");
$stmt->bind_param("i", $userinfo["idcentro"]);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 1) {
    $schoolurl = $result->fetch_row()[0];
}

// Copy all user uploaded files
$source = $uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/";
recursivecopy($source, $baseurl);

// Copy all theme-specific assets
$source = "themes/{$theme}";
recursivecopy($source, $baseurl);

// Copy all common assets
$source = "themes/common";
recursivecopy($source, $baseurl);

copy("../../favicon.ico",  $baseurl.'/favicon.ico');

mkdir($baseurl."/assets", 0755, true);

// -- Yearbook data -- //

// Teachers
$teachers_js = json_encode($teachers, JSON_PRETTY_PRINT);
// Students
$students_js = json_encode($students, JSON_PRETTY_PRINT);
// Gallery
$gallery_js = json_encode($gallery, JSON_PRETTY_PRINT);
// Yearbook info
$ybinfo = [
    "schoolname" => $userinfo["namecentro"],
    "schoolurl" => $schoolurl,
    "year" => $userinfo["yearuser"],
    "acyear" => $acyear,
    "ybdate" => $dt->getTimestamp()
];

if ($theme == "default") {
    $ybinfo["banner"] = null;
    // Copy banner
    $tmpFilePath = $_FILES['banner']['tmp_name'];
    if($tmpFilePath != "") {
        $uploadName = $_FILES['banner']['name'];
        $ext = pathinfo($uploadName, PATHINFO_EXTENSION);
        if (!in_array($ext, ["jpg", "jpeg", "png", "gif"])) {
            $response = [
                "code" => "E",
                "description" => "El banner tiene una extensión no soportada"
            ];
            sendJSON($response);
        }
        else {
            $ybinfo["banner"] = "banner.$ext";
            move_uploaded_file($tmpFilePath, "$baseurl/assets/banner.$ext");
        }
    }
}

$ybinfo_js = json_encode($ybinfo, JSON_PRETTY_PRINT);

// Data to be written in js file
$data = "
// Teachers
const teachers_js = {$teachers_js};
// Students
const students_js = {$students_js};
// Gallery
const gallery_js = {$gallery_js};
// Yearbook info
const ybinfo_js = {$ybinfo_js};
";

file_put_contents("{$baseurl}/scripts/data.js", $data);
// ZIP class

// https://stackoverflow.com/a/19730838 Generate ZIP file from yearbook folder
class HZip 
{ 
  /** 
   * Add files and sub-directories in a folder to zip file. 
   * @param string $folder 
   * @param ZipArchive $zipFile 
   * @param int $exclusiveLength Number of text to be exclusived from the file path. 
   */ 
  private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
    $handle = opendir($folder);
    while (false !== $f = readdir($handle)) {
      if ($f != '.' && $f != '..') {
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip.
        $localPath = "yearbook".substr($filePath, $exclusiveLength);
        if (is_file($filePath)) {
          $zipFile->addFile($filePath, $localPath);
        } elseif (is_dir($filePath)) {
          // Add sub-directory.
          $zipFile->addEmptyDir($localPath);
          self::folderToZip($filePath, $zipFile, $exclusiveLength);
        }
      }
    }
    closedir($handle);
  }
  /**
   * Zip a folder (include itself).
   * Usage:
   *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
   *
   * @param string $sourcePath Path of directory to be zip.
   * @param string $outZipPath Path of output zip file.
   */
  public static function zipDir($sourcePath, $outZipPath)
  {
    $pathInfo = pathInfo($sourcePath);
    $dirName = $pathInfo['basename'];

    $z = new ZipArchive();
    $z->open($outZipPath, ZIPARCHIVE::CREATE);
    self::folderToZip($sourcePath, $z, strlen("$sourcePath"));
    $z->close();
  }
}

// Makes zip from folder
$date_file = $dt->format('d-m-Y');
$zip_name = "yearbook_".$date_file.'.zip';
HZip::zipDir($baseurl, $baseurl."/".$zip_name);

// Writes data to DB
$stmt = $db->prepare("INSERT INTO yearbooks(schoolid, schoolname, schoolyear, zipname, acyear) VALUES(?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $userinfo["idcentro"], $userinfo["namecentro"], $userinfo["yearuser"], $zip_name, $acyear);
if ($stmt->execute() !== true) {
    $response = [
        "code" => "E",
        "description" => "Error al escribir en la base de datos"
    ];
    sendJSON($response);
}

// Everyting went OK
$response = [
    "code" => "C",
    "description" => "Yearbook generado con éxito"
];

sendJSON($response);
?>
