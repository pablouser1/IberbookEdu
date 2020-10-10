<?php
if (!isset($_SESSION, $_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../helpers/db/db.php");
$db = new DB;
// Get all staff members
$stmt = $db->prepare("SELECT id, username, permissions FROM staff");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row["id"];
    $staff[$id] = array();
    foreach ($row as $value) {
        $staff[$id][] = $value;
    }
}
$stmt->close();
?>
