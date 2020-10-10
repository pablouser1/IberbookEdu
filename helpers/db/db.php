<?php
require_once("dbconf.php");

class DB {
  private $conn;

  function __construct() {
    $logininfo = [
      "name" => $GLOBALS["db_name"],
      "host" => $GLOBALS["db_host"],
      "port" => $GLOBALS["db_port"],
      "username" => $GLOBALS["db_username"],
      "password" => $GLOBALS["db_password"]
    ];
    $this->conn = new mysqli($logininfo["host"], $logininfo["username"], $logininfo["password"], $logininfo["name"], $logininfo["port"]);
    // Check connection
    if ($this->conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
  }

  function __destruct() {
    $this->conn->close();
  }
  public function query($sql) {
    return $this->conn->query($sql);
  }
  public function prepare($sql) {
    return $this->conn->prepare($sql);
  }
}

?>
