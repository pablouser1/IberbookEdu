<?php
session_start();
if (isset($_SESSION["owner"])){
    header("Location: owner/dashboard.php");
    exit;
}

$login_error = array();
require_once("../helpers/db.php");
$db = new DB;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!$_POST["username"]){
        $login_error[] = "You didn't type a username";
    }
    else{
        $username = trim($_POST["username"]);
    }
    // Check if password is empty
    if(!$_POST["password"]){
        $login_error[] = "You didn't type a password";
    } 
    else{
        $password = trim($_POST["password"]);
    }

    if (empty($login_error)) {
        // Prepare a select statement
        $stmt = $db->prepare("SELECT `password` FROM staff WHERE username = ? and permissions='owner'");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($hashed_password);
                $stmt->fetch();
                if (password_verify($password, $hashed_password)) {
                    // Store data in session variables
                    $_SESSION["owner"] = true;
                    $ownerinfo = [
                        "username" => $username
                    ];
                    $_SESSION["ownerinfo"] = $ownerinfo;
                    $stmt->close();
                    // Redirect user to welcome page
                    header("Location: owner/dashboard.php");
                    exit;
                }
                else {
                    // Display an error message if password is not valid
                    $login_error[] = "Invalid password";
                }
            }
            else {
                $login_error[] = "That user doesn't exist";
            }
        }
        else {
            $login_error[] = "There was an error processing your request, try again later";
        }
        $stmt->close();
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
    <link rel="stylesheet" href="../assets/styles/login.css"/>
</head>

<body>
    <section class="container">
        <div class="columns is-multiline">
            <div class="column is-8 is-offset-2 login">
                <div class="columns">
                    <div class="column left">
                        <h1 class="title is-1">IberbookEdu</h1>
                        <h2 class="subtitle colored is-4">Login with your credentials</h2>
                        <div class="notification is-danger <?php if(!$login_error) echo("is-hidden"); ?>">
                            <span>
                                <p>There was an error processing your request:</p>
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
                                <label class="label">Username</label>
                                <div class="control">
                                    <input name="username" class="input is-medium" type="text" placeholder="user">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Password</label>
                                <div class="control">
                                    <input name="password" class="input is-medium" type="password" placeholder="**********">
                                </div>
                            </div>
                            <button class="button is-block is-primary is-fullwidth is-medium">Login</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="column is-8 is-offset-2">
                <nav class="level">
                    <div class="level-left">
                        <div class="level-item">
                            Made with ❤️ in Github
                        </div>
                    </div>
                    <div class="level-right">
                        <a class="level-item" href="https://github.com/pablouser1/IberbookEdu-backend">About</a>
                    </div>
                </nav>
            </div>
        </div>
    </section>
</body>

</html>