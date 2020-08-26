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

/** https://stackoverflow.com/a/30010928
 * Creates a random unique temporary directory, with specified parameters,
 * that does not already exist (like tempnam(), but for dirs).
 *
 * Created dir will begin with the specified prefix, followed by random
 * numbers.
 *
 * @link https://php.net/manual/en/function.tempnam.php
 *
 * @param string|null $dir Base directory under which to create temp dir.
 *     If null, the default system temp dir (sys_get_temp_dir()) will be
 *     used.
 * @param string $prefix String with which to prefix created dirs.
 * @param int $mode Octal file permission mask for the newly-created dir.
 *     Should begin with a 0.
 * @param int $maxAttempts Maximum attempts before giving up (to prevent
 *     endless loops).
 * @return string|bool Full path to newly-created dir, or false on failure.
 */
function tempdir($dir = null, $prefix = 'test_', $mode = 0700, $maxAttempts = 1000)
{
    /* Use the system temp dir by default. */
    if (is_null($dir))
    {
        $dir = sys_get_temp_dir();
    }

    /* Trim trailing slashes from $dir. */
    $dir = rtrim($dir, DIRECTORY_SEPARATOR);

    /* If we don't have permission to create a directory, fail, otherwise we will
     * be stuck in an endless loop.
     */
    if (!is_dir($dir) || !is_writable($dir))
    {
        return false;
    }

    /* Make sure characters in prefix are safe. */
    if (strpbrk($prefix, '\\/:*?"<>|') !== false)
    {
        return false;
    }

    /* Attempt to create a random directory until it works. Abort if we reach
     * $maxAttempts. Something screwy could be happening with the filesystem
     * and our loop could otherwise become endless.
     */
    $attempts = 0;
    do
    {
        $path = sprintf('%s%s%s%s', $dir, DIRECTORY_SEPARATOR, $prefix, mt_rand(100000, mt_getrandmax()));
    } while (
        !mkdir($path, $mode) &&
        $attempts++ < $maxAttempts
    );
    return $path;
}

$userinfo = $_SESSION["userinfo"];

// Zip file location
$zipdir = $ybpath.$userinfo["idcentro"].'/'.$userinfo["yearuser"];
$_SESSION["zipdir"] = $zipdir;

// Students

$stmt = $conn->prepare("SELECT id, fullname, picname, vidname, link, quote, uploaded FROM students where schoolid=? and schoolyear=?");
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
        "quote" => $row["quote"], // User quote
        "date" => $row["uploaded"], // Actual date
    ];
}
$stmt->close();

// Teachers

$stmt = $conn->prepare("SELECT id, fullname, picname, vidname, link, quote, uploaded, subject FROM teachers where schoolid=? and schoolyear=?");
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
        "quote" => $row["quote"], // User quote
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

$tempdir = tempdir();

// Set vars to session
$_SESSION["students"] = $students;
$_SESSION["teachers"] = $teachers;
$_SESSION["gallery"] = $gallery;
$_SESSION["tempdir"] = $tempdir;

// Copying all files to final folder
$source = $ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/uploads/";
$dest = $tempdir;
recursivecopy($source, $dest);

$source = "../assets/yearbook";
recursivecopy($source, $tempdir);
copy("../LICENSE",  $tempdir.'/LICENSE.txt');
copy("../favicon.ico",  $tempdir.'/favicon.ico');

header("Location: generate.php");
?>
