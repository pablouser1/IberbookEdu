<?php
session_start();
if (isset($_SESSION["loggedin"]) && isset($_SESSION["userinfo"])){
    $userinfo = $_SESSION["userinfo"];
    if($_SESSION["loggedin"] == "user"){
        header("Location: users/dashboard.php");
    }
    elseif($_SESSION["loggedin"] == "admin"){
        header("Location: profiles/admin.php");
    }
}
$login_error = $password_err = $username_err = "";
require_once("helpers/db.php");
require_once("helpers/api.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Reset SESSION
    $_SESSION = array();
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
                    $login_error = "Su centro no está permitido";
                }
            }
            // Check if user is admin
            $sql = "SELECT `username`, `permissions` FROM `staff` WHERE username ='$username' and permissions='admin'";
            $result = $conn->query($sql);
            if ($result->num_rows == 1) {
                // User is admin
                $_SESSION["loggedin"] = "admin";
                switch ($userinfo["typeuser"]){
                    case "ALU":
                        $_SESSION["userinfo"] = $userinfo;
                        header("Location: profiles/admin.php");
                    break;
                    case "TUT_LEGAL":
                        $_SESSION["tutorinfo"] = $userinfo;
                    break;
                    case "P":
                        $_SESSION["teacherinfo"] = $userinfo;
                    break;
                }
            }
            else{
                // User is not admin
                $_SESSION["loggedin"] = "user";
            }

            switch ($userinfo["typeuser"]){
                case "ALU":
                    // Only if user is student and regular user.
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
        else{
            $login_error = $loginres["error"];
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
    <script src="scripts/login.js"></script>
</body>

</html>
