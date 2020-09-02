<?php
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("location: ../login.php");
}

function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
}

require_once("../helpers/db.php");
require_once("../helpers/config.php");

$userinfo = $_SESSION["userinfo"];
// Year without spaces
$yearuser = str_replace(' ', '', $userinfo["yearuser"]);

if (isset($_GET["action"])) {
    // Get academic year (2020-2021 for example)
    $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
    switch ($_GET["action"]) {
        case "delete":
            // Delete yearbook
            $stmt = $conn->prepare("DELETE FROM yearbooks WHERE schoolid=? and schoolyear=? and acyear=?");
            $stmt->bind_param("iss", $userinfo["idcentro"], $userinfo["yearuser"], $acyear);
            if ($stmt->execute() !== true) {
                die("Error updating record: " . $conn->error);
            }
            $stmt->close();
            recursiveRemoveDirectory($_SERVER["DOCUMENT_ROOT"].$ybpath.$userinfo["idcentro"]."/$acyear/".$yearuser);
        break;
    }
}

header("Location: dashboard.php");
?>
