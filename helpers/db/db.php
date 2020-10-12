<?php
require_once("dbconf.php");

class DB {
  private $conn;

  function __construct() {
    $dbinfo = [
      "name" => $GLOBALS["db_name"],
      "host" => $GLOBALS["db_host"],
      "port" => $GLOBALS["db_port"],
      "username" => $GLOBALS["db_username"],
      "password" => $GLOBALS["db_password"]
    ];
    $this->conn = new mysqli($dbinfo["host"], $dbinfo["username"], $dbinfo["password"], $dbinfo["name"], $dbinfo["port"]);
    // Check connection
    if ($this->conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
  }

  // Close db connection when destroyed
  function __destruct() {
    $this->conn->close();
  }
  
  // Query instruction
  public function query($sql) {
    return $this->conn->query($sql);
  }

  // Prepared statement
  public function prepare($sql) {
    return $this->conn->prepare($sql);
  }
}

?>
