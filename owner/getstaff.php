<?php
if(!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["owner"])){
    header("Location: login.php");
}
require_once("../helpers/db.php");

// Get all staff members
$stmt = $conn->prepare("SELECT id, username, permissions FROM staff");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row["id"];
    $staff[$id] = array();
    foreach ($row as $value) {
        $staff[$id][] = $value;
    }
}
?>
