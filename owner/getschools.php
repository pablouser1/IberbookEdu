<?php
if(!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
}
require_once("../helpers/db/db.php");

// Get all schools
$stmt = $conn->prepare("SELECT id, url FROM schools");
$stmt->execute();
$result = $stmt->get_result();
$schools = [];
while ($row = mysqli_fetch_assoc($result)) {
    $schools[] = [
        "id" => $row["id"],
        "url" => (empty($row["url"]))? "-" : $row["url"]
    ];
}
$stmt->close();
?>
