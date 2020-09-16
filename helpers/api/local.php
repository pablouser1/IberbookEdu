<?php
// TODO, add support for teachers and get school name
require_once(__DIR__. "/../config.php");
require_once(__DIR__. "/../db/db.php");
class Api {
    private $conn;
    private $type;
    private $userid;

    function __construct() {
        // Class from requests.php
        $this->conn = $GLOBALS["conn"];
    }

    function login($username, $password, $type) {
        // Prepare a select statement
        $sql = "SELECT id, password FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($this->conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            // Set parameters
            $param_username = $username;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // User logged in correctly
                            $this->type = $type;
                            $this->userid = (int)$id;
                            return [
                                "code" => "C",
                                "description" => null
                            ];
                        } else{
                            // Display an error message if password is not valid
                            $login_error = "esta contraseña no es válida.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $login_error = "no existe ninguna cuenta con este nombre de usuario.";
                }
            } else{
                $login_error = "por favor inténtelo más tarde";
            }
            // Close statement
            $stmt->close();
        }
        if ($login_error) {
            return [
                "code" => "E",
                "description" => "Ha habido un error, {$login_error}"
            ];
        }
    }

    function getinfo() {
        $stmt = $this->conn->prepare("SELECT `id`, `fullname`, `type`, `schoolid`, `group` FROM users WHERE id=?");
        $stmt->bind_param("i", $this->userid);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $userinfo = [
                "iduser" => $this->userid,
                "nameuser" => $row["fullname"],
                "typeuser" => $row["type"],
                "yearuser" => $row["group"],
                "photouser" => base64_encode(file_get_contents(__DIR__. "/../../assets/img/PortraitPlaceholder.png")),
                "idcentro" => $row["schoolid"],
                "namecentro" => "Test"
            ];
        }
        $stmt->close();
        return $userinfo;
    }
}

?>
