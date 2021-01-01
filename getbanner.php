<?php
// Gets random banner, used in home page
require_once("functions.php");
require_once("headers.php");

require_once("config/config.php");
require_once("helpers/db.php");

$db = new DB;
$sql = "SELECT id, schoolid, schoolname, schoolyear, acyear, banner FROM yearbooks ORDER BY RAND() LIMIT 1";
$result = $db->query($sql);
if ($result->num_rows === 1) {
    while($row = $result->fetch_assoc()) {
        $yearbook = [
            "id" => $row["id"],
            "schoolname" => $row["schoolname"],
            "schoolyear" => $row["schoolyear"],
            "acyear" => $row["acyear"],
            "url" => "/yearbooks/".$row["id"]."/assets/".$row["banner"]
        ];
    }
}
else {
    $response = [
        "code" => "E"
    ];
    sendJSON($response);
}

$response = [
    "code" => "C",
    "data" => $yearbook
];
sendJSON($response);
?>
