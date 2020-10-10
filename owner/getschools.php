<?php
if (!isset($_SESSION, $_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}
require_once("../helpers/db/db.php");
$db = new DB;
// Get all schools
$stmt = $db->prepare("SELECT id, url FROM schools");
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
