<?php
session_start();
if (isset($_SESSION["loggedin"]) && isset($_SESSION["userinfo"])){
    $userinfo = $_SESSION["userinfo"];
    header("Location: users/dashboard.php");
    exit;
}
$login_error = array();
require_once("helpers/db.php");
require_once("helpers/api.php");

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
    if(!$login_error){
        // Get type
        $type = $_POST["type"];
        // Login user to pasen and check if there are any errors
        $loginres = login($username, $password, $type);
        if (!$loginres["error"]){
            // Get user info
            $userinfo = getinfo($loginres["cookies"], $type);
            // Check if school is allowed, only for students. Teachers' schools are checked in users/teachers.php
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
                exit;
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
                    <p class="subtitle has-text-black">Por favor, inicia sesión con tu cuenta de PASEN/SENECA o ROBLE.</p>
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
                                        <option value="alumno">Alumno</option>
                                        <option value="tutorlegal">Tutor legal</option>
                                        <option value="profesor">Profesor</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="button is-block is-info is-fullwidth">Iniciar sesión</button>
                        </form>
                        <p class="has-text-grey">
                            <a href="about.html">Acerca de</a>
                        </p>
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

