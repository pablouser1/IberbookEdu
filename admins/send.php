<?php
// -- Build variables with database data and send it to generate.php -- //

session_start();
// Check if the user is logged in, if not then redirect him to login page
if($_SESSION["loggedin"] !== "admin"){
    header("location: login.php");
    exit;
}
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
require_once("../helpers/db.php");
require_once("../helpers/config.php");
$userinfo = $_SESSION["userinfo"];

// Zip files location
$baseurl = $ybpath.$userinfo["idcentro"].'/'.$userinfo["yearuser"].'/generated/';
$_SESSION["baseurl"] = $baseurl;

// Students

$stmt = $conn->prepare("SELECT id, fullname, picname, vidname, link, uploaded FROM students where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$students_dir = 'students/';
while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $students[] = [
        "id" => $id,
        "photo" => $students_dir.$id.'/'.$row["picname"],
        "name" => getname("abbr", $row["fullname"]),
        "items" => [
            [
                "id" => $id,
                "type" => "video",
                "src" => $students_dir.$id.'/'.$row["vidname"],
                "time" => strtotime($row["uploaded"]),
                "link" => $row["link"]
            ]
        ],
        "fullname" => getname("full", $row["fullname"]),
        "date" => $row["uploaded"], // Actual date
    ];
}
$stmt->close();

// Teachers

$stmt = $conn->prepare("SELECT id, fullname, picname, vidname, link, uploaded, subject FROM teachers where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$teachers_dir = 'teachers/';
while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $teachers[] = [
        "id" => $id,
        "photo" => $teachers_dir.$id.'/'.$row["picname"],
        "name" => getname("abbr", $row["fullname"]),
        "items" => [
            [
                "id" => $id,
                "type" => "video",
                "src" => $teachers_dir.$id.'/'.$row["vidname"],
                "time" => strtotime($row["uploaded"]), // Change time to Unix Timestamp, used for "x hours ago"
                "link" => $row["link"]
            ]
        ],
        // Additional info not used by zuck.js
        "subject" => $row["subject"],
        "fullname" => getname("full", $row["fullname"]),
        "date" => $row["uploaded"], // Actual date
    ];
}
$stmt->close();

// Gallery

$stmt = $conn->prepare("SELECT picname, picdescription FROM gallery where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$gallery = array();
$gallery_dir = 'gallery/';
$i = 0;
while($row = mysqli_fetch_assoc($result)) {
    $gallery[$i] = array();
    $gallery[$i]["path"] = $gallery_dir.$row["picname"];
    $gallery[$i]["description"] = $row["picdescription"];
    $i++;
}
$stmt->close();

// Set vars to session
$_SESSION["students"] = $students;
$_SESSION["teachers"] = $teachers;
$_SESSION["gallery"] = $gallery;

// Copying all files to final folder
$source = $ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/uploads/";
$dest = $baseurl;

if (!is_dir($dest."scripts/vendor")){
    mkdir($dest."scripts/vendor", 0755, true);
}

if (!is_dir($dest."styles/vendor")){
    mkdir($dest."styles/vendor", 0755, true);
}
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

header("Location: generate.php");
?>
