<?php
require_once("db_config.php");

// Create connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name, $db_port);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
