<?php
session_start();
if (isset($_SESSION["loggedin"]) && isset($_SESSION["userinfo"])){
    $userinfo = $_SESSION["userinfo"];
    header("Location: users/dashboard.php");
}
$login_error = array();
require_once("helpers/db.php");
require_once("helpers/api.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Reset SESSION
    $_SESSION = array();
    if(empty(trim($_POST["username"]))){
        $login_error = "No has escrito ningún nombre de usuario.";
    }
    else{
        $username = trim($_POST["username"]);
    }
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $login_error = "No has escrito ninguna contraseña.";
    } 
    else{
        $password = trim($_POST["password"]);
    }
    if(empty($login_error)){
        // Get type
        $type = $_POST["type"];
        // Login user to pasen and check if there are any errors
        $loginres = login($username, $password, $type);
        $pos = strpos($loginres["error"], "error");
        if ($pos === false){
            // Get user info
            $userinfo = getinfo($loginres["cookies"], $type);
            // Check if school is allowed, only for students. Teachers' schools are checked in users/teachers.php
            if ($userinfo["typeuser"] == "ALU"){
                $sql = "SELECT `id` FROM `schools` WHERE id=$userinfo[idcentro]";
                $result = $conn->query($sql);
                if ($result !== false && $result->num_rows == 0) {
                    $login_error[] = "Su centro no está permitido";
                }

                //Check if user is from 4º ESO or 2ºBCT    
                if((strpos($userinfo["yearuser"], "4º ESO") || strpos($userinfo["yearuser"], "2º BCT") === false)) {
                    $login_error[] = "Sólo se admiten usuarios de 4º ESO o de 2º BACH";
                }
            }
            if(empty($login_error)){
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
                        header("Location: users/dashboard.php");
                    break;
                    case "TUT_LEGAL":
                        $_SESSION["tutorinfo"] = $userinfo;
                        header("Location: profiles/tutorlegal.php");
                    break;
                    case "P":
                        $_SESSION["teacherinfo"] = $userinfo;
                        header("Location: profiles/teachers.php");
                    break;
                }
            }
        }
        else{
            $login_error[] = $loginres["error"];
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iberbook Login</title>
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
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
                                <label for="" class="label">Soy...</label>
                                <div class="select">
                                    <select name="type">
                                        <option value="alumno">Alumno</option>
                                        <option value="tutorlegal">Tutor legal</option>
                                        <option value="profesor">Profesor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field">
                                <button type="submit" class="button is-success">
                                    Login
                                </button>
                            </div>
                            <a href="about.html">Acerca de</a>
                        </form>
                        <div class="notification is-danger <?php if(empty($login_error)) echo('is-hidden');?>">
                            <span>
                                <?php
                                if(!empty($login_error)){
                                    foreach($login_error as $error){
                                        echo <<<EOL
                                        <p>Hubo un error al iniciar sesión:<br>$error</p>
                                        EOL;
                                    }
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="scripts/login.js"></script>
</body>

</html>
