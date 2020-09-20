<?php
session_start();
if (isset($_SESSION["loggedin"], $_SESSION["userinfo"])){
    $userinfo = $_SESSION["userinfo"];
    header("Location: users/dashboard.php");
    exit;
}
elseif (isset($_SESSION["owner"])) {
    $ownerinfo = $_SESSION["ownerinfo"];
    header("Location: owner/dashboard.php");
}

$login_error = array();
require_once("helpers/db/db.php");
require_once("helpers/api.php");
$api = new Api;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!$_POST["username"]){
        $login_error[] = "No has escrito ningún nombre de usuario.";
    }
    else{
        $username = trim($_POST["username"]);
    }
    // Check if password is empty
    if(!$_POST["password"]){
        $login_error[] = "No has escrito ninguna contraseña.";
    } 
    else{
        $password = trim($_POST["password"]);
    }
    
    // Get type
    $type = $_POST["type"];

    if (!$login_error) {
        // -- User is not owner, using API login system -- //
        if ($type !== "owner") {
            // Login user to pasen and check if there are any errors
            $loginres = $api->login($username, $password, $type);
            if ($loginres["code"] === "C"){
                // Get user info
                $userinfo = $api->getinfo();
                // Check if school is allowed, only for students. Teachers and parents are checked in users/teachers.php and users/tutorlegal.php
                if ($userinfo["typeuser"] == "ALU"){
                    $sql = "SELECT `id` FROM `schools` WHERE id=$userinfo[idcentro]";
                    $result = $conn->query($sql);
                    if ($result !== false && $result->num_rows == 0) {
                        $login_error[] = "Su centro no está permitido";
                    }
                    // Check if user is from 4º ESO, 2º BCT or 6º Primaria
                    if(!preg_match("/(4º\sESO)|(2º\sBCT)|(6.)P/", $userinfo["yearuser"])) {
                        $login_error[] = "Sólo se admiten usuarios de 4º ESO, 2º BACH o 6º Primaria";
                    }
                }
                if(!$login_error){
                    // Check if user is admin
                    $sql = "SELECT `username`, `permissions` FROM `staff` WHERE username ='$username' and permissions='admin'";
                    $result = $conn->query($sql);
                    if ($result->num_rows == 1) {
                        // User is admin
                        $_SESSION["loggedin"] = "admin";
                    }
                    else{
                        // User is not admin
                        $_SESSION["loggedin"] = "user";
                    }
                    switch ($userinfo["typeuser"]){
                        case "ALU":
                            $_SESSION["userinfo"] = $userinfo;
                            header("Location: index.php");
                        break;
                        case "TUT_LEGAL":
                            $_SESSION["tutorinfo"] = $userinfo;
                            header("Location: profiles/tutorlegal.php");
                        break;
                        case "P":
                            $_SESSION["teacherinfo"] = $userinfo;
                            $api->settype("students");
                            header("Location: profiles/teachers.php");
                        break;
                    }
                    exit;
                }
            }
            else{
                $login_error[] = $loginres["description"];
            }
        }

        // -- User is owner (login using own DB) -- //
        else {
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
                                header("location: owner/dashboard.php");
                                exit;
                            } else{
                                // Display an error message if password is not valid
                                $login_error[] = "Esta contraseña no es válida.";
                            }
                        }
                    } else{
                        // Display an error message if username doesn't exist
                        $login_error[] = "No existe ninguna cuenta con este nombre de usuario.";
                    }
                } else{
                    $login_error[] = "Ha habido un error, por favor inténtelo más tarde";
                }
                // Close statement
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - IberbookEdu</title>
    <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
</head>

<body>
    <section class="hero is-success is-fullheight">
        <div class="hero-body">
            <div class="container has-text-centered">
                <div class="column is-4 is-offset-4">
                    <h3 class="title has-text-black">IberbookEdu - Login</h3>
                    <hr class="login-hr">
                    <p class="subtitle has-text-black">Por favor, inicia sesión con tus credenciales.</p>
                    <div class="box">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="field">
                                <label class="label">Usuario</label>
                                <div class="control has-icons-left">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input name="username" type="text" placeholder="usuario" class="input" required>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Contraseña</label>
                                <div class="control has-icons-left">
                                    <span class="icon is-small is-left">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <input name="password" type="password" placeholder="**********" class="input" required>
                                </div>
                            </div>
                            <div class="field">
                                <label for="" class="label">Soy...</label>
                                <div class="select">
                                    <select name="type">
                                        <option value="students">Alumno</option>
                                        <option value="tutorlegal">Tutor legal</option>
                                        <option value="teachers">Profesor</option>
                                        <option value="owner">Dueño</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="button is-block is-info is-fullwidth">Iniciar sesión</button>
                        </form>
                        <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
                            <ul>
                                <li>
                                    <a href="index.php">Volver al inicio</a>
                                </li>
                                <li>
                                    <a href="about.html">Acerca de</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="notification is-danger <?php if(!$login_error) echo("is-hidden"); ?>">
                    <span>
                        <p>Hubo un error al procesar tu solicitud:</p>
                        <?php
                        if($login_error) {
                            foreach($login_error as $error) {
                                echo "<p>$error</p>";
                            }
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </section>
</body>

</html>

