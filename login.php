<?php
session_start();
if (isset($_SESSION["loggedin"])){
    if ($_SESSION["loggedin"] === "owner") {
        header("Location: owner/dashboard.php");
        exit;
    }
    else {
        header("Location: users/dashboard.php");
        exit;
    }
}

$login_error = array();
require_once("helpers/db/db.php");
require_once("helpers/api.php");
$api = new Api;
$db = new DB;
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
                if ($userinfo["typeuser"] == "students"){
                    $sql = "SELECT `id` FROM `schools` WHERE id=$userinfo[idcentro]";
                    $result = $db->query($sql);
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
                    $result = $db->query($sql);
                    if ($result->num_rows == 1) {
                        // User is admin
                        $_SESSION["loggedin"] = "admin";
                    }
                    else{
                        // User is not admin
                        $_SESSION["loggedin"] = "user";
                    }
                    switch ($userinfo["typeuser"]){
                        case "students":
                            // Create profile
                            require_once("helpers/createprofile.php");
                            // Get id from DB
                            $profile = createprofile($userinfo, "students");
                            $userinfo["id"] = $profile;
                            // Set session
                            $_SESSION["userinfo"] = $userinfo;
                            header("Location: index.php");
                            exit;
                        break;
                        case "tutor":
                            $_SESSION["tutorinfo"] = $userinfo;
                            header("Location: profiles/tutorlegal.php");
                            exit;
                        break;
                        case "teachers":
                            $_SESSION["teacherinfo"] = $userinfo;
                            header("Location: profiles/teachers.php");
                            exit;
                        break;
                    }
                    exit;
                }
            }
            else{
                $login_error[] = $loginres["error"];
            }
        }

        // -- User is owner (login using own DB) -- //
        else {
            // Prepare a select statement
            $sql = "SELECT username, password FROM staff WHERE username = ? and permissions='owner'";
            if($stmt = $db->prepare($sql)){
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
                                header("Location: owner/dashboard.php");
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
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="assets/styles/login.css"/>
</head>

<body>
    <section class="container">
        <div class="columns is-multiline">
            <div class="column is-8 is-offset-2 login">
                <div class="columns">
                    <div class="column left">
                        <h1 class="title is-1">IberbookEdu</h1>
                        <h2 class="subtitle colored is-4">Inicia sesión con tus credenciales</h2>
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
                    <div class="column right has-text-centered">
                        <!-- FORM -->
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="field">
                                <label class="label">Nombre de usuario</label>
                                <div class="control">
                                    <input name="username" class="input is-medium" type="text" placeholder="usuario">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Contraseña</label>
                                <div class="control">
                                    <input name="password" class="input is-medium" type="password" placeholder="**********">
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
                            <button class="button is-block is-primary is-fullwidth is-medium">Iniciar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="column is-8 is-offset-2">
                <nav class="level">
                    <div class="level-left">
                        <div class="level-item">
                            Hecho con ❤️ en Github
                        </div>
                    </div>
                    <div class="level-right">
                        <a class="level-item" href="about.html">Acerca de</a>
                    </div>
                </nav>
            </div>
        </div>
    </section>
</body>

</html>