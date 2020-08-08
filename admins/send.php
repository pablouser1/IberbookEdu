<?php
// -- Build variables with database data and send it to generate.php -- //

session_start();
// Check if the user is logged in, if not then redirect him to login page
if($_SESSION["loggedin"] !== "admin"){
    header("location: login.php");
    exit;
}
function separetename($part, $string){
    if ($part == "name"){
        return substr($string, strpos($string, ",") + 2);
    }
    else{
        return strtok($string, ',');
    }
}
require_once("../helpers/db.php");
require("../helpers/common.php");
$userinfo = $_SESSION["userinfo"];

// Zip files location
$baseurl = '../yearbooks/'.$userinfo["idcentro"].'/'.$userinfo["yearuser"].'/generated/';
$_SESSION["baseurl"] = $baseurl;

// Students

$stmt = $conn->prepare("SELECT id, fullname, picname, vidname FROM students where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$students = array();
$students_dir = 'students/';
while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $students[$id] = array();
    $students[$id]["name"] = separetename("name", $row["fullname"]);
    $students[$id]["surnames"] = separetename("surname", $row["fullname"]);
    $students[$id]["pic"] = $students_dir.$id.'/'.$row["picname"];
    $students[$id]["video"] = $students_dir.$id.'/'.$row["vidname"];
}
$stmt->close();

// Teachers

$stmt = $conn->prepare("SELECT id, fullname, picname, vidname, subject FROM teachers where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
$teachers = array();
$teachers_dir = 'teachers/';
while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $teachers[$id] = array();
    $teachers[$id]["name"] = separetename("name", $row["fullname"]);
    $teachers[$id]["surnames"] = separetename("surname", $row["fullname"]);
    $teachers[$id]["subject"] = $row["subject"];
    $teachers[$id]["pic"] = $teachers_dir.$id.'/'.$row["picname"];
    $teachers[$id]["video"] = $teachers_dir.$id.'/'.$row["vidname"];
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
$source = "../yearbooks/".$userinfo["idcentro"]."/".$userinfo["yearuser"]."/uploads/";
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
