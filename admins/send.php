<?php
// -- Build variables with database data, copy files to temp folder and continue with generate.php -- //

session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== "admin") {
    header("Location: ../login.php");
}
require_once("../helpers/db.php");
require_once("../helpers/config.php");

// -- Functions -- //
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

$userinfo = $_SESSION["userinfo"];
// Used later for directories without spaces
$yearuser = str_replace(' ', '', $userinfo["yearuser"]);

// Get academic year (2020/2021 for example)
$acyear = date("Y",strtotime("-1 year"))."-".date("Y");
if(!is_dir($_SERVER["DOCUMENT_ROOT"].$ybpath.$userinfo["idcentro"].'/'.$acyear."/".$yearuser)) {
    mkdir($_SERVER["DOCUMENT_ROOT"].$ybpath.$userinfo["idcentro"].'/'.$acyear."/".$yearuser, 0755, true);
}

// Zip file location
$baseurl = $_SERVER["DOCUMENT_ROOT"].$ybpath.$userinfo["idcentro"].'/'.$acyear."/".$yearuser;
$_SESSION["baseurl"] = $baseurl;

// Teachers
$stmt = $conn->prepare("SELECT id, fullname, photo, video, link, quote, uploaded, subject FROM teachers where schoolid=? and schoolyear=?");
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
$stmt = $conn->prepare("SELECT id, fullname, photo, video, link, quote, uploaded FROM students where schoolid=? and schoolyear=?");
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

// Gallery
$stmt = $conn->prepare("SELECT name, description, type FROM gallery where schoolid=? and schoolyear=?");
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
$stmt = $conn->prepare("SELECT url FROM schools where id=?");
$stmt->bind_param("i", $userinfo["idcentro"]);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 1) {
    $schoolurl = $result->fetch_row()[0];
}

// Set vars to session
$_SESSION["students"] = $students;
$_SESSION["teachers"] = $teachers;
$_SESSION["gallery"] = $gallery;
$_SESSION["schoolurl"] = $schoolurl;
$_SESSION["acyear"] = $acyear;

// Copying all files to final folder
$source = $uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/";
$dest = $baseurl;
recursivecopy($source, $dest);

$source = "../assets/yearbook";
// Copy all assets/yearbook dir
recursivecopy($source, $dest);

copy("../favicon.ico",  $baseurl.'/favicon.ico');

header("Location: generate.php");
?>

