<?php
session_start();
$login_error = $password_err = $username_err = "";
require_once("../helpers/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(empty(trim($_POST["username"]))){
        $username_err = "No has escrito ningún nombre de usuario.";
    }
    else{
        $username = trim($_POST["username"]);
    }
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "No has escrito ninguna contraseña.";
    } 
    else{
        $password = trim($_POST["password"]);
    }
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT username, password FROM staff WHERE username = ? and permissions='owner'";
        
        if($stmt = mysqli_prepare($conn, $sql)){
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
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            
                            // Store data in session variables
                            $_SESSION["owner"] = true;
                            $ownerinfo = [
                                "username" => $username
                            ];
                            $_SESSION["ownerinfo"] = $ownerinfo;
                            // Redirect user to welcome page
                            header("location: dashboard.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "Esta contraseña no es válida.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No existe ninguna cuenta con este nombre de usuario.";
                }
            } else{
                $login_error = "Ha habido un error, por favor inténtelo más tarde";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Yearbook</title>
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css" />
</head>

<body>
    <section class="hero is-primary is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-5-tablet is-4-desktop is-3-widescreen">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="box">
                            <div class="field">
                                <label for="" class="label">Usuario</label>
                                <div class="control has-icons-left">
                                    <input name="username" type="text" placeholder="usuario" class="input" required>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="field">
                                <label for="" class="label">Contraseña</label>
                                <div class="control has-icons-left">
                                    <input name="password" type="password" placeholder="*******" class="input" required>
                                    <span class="icon is-small is-left">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="field">
                                <button type="submit" class="button is-success">
                                    Login
                                </button>
                            </div>
                            <a href="about.html">Acerca de</a>
                        </form>
                        <?php
                        if($login_error || $username_err || $password_err !== ""){
                            echo <<<EOL
                            <div class="notification is-danger">
                                <span>
                                    <p>$login_error</p>
                                    <p>$username_err $password_err</p>
                                </span>
                            </div>
                            EOL;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>