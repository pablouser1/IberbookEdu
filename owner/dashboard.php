<?php
session_start();
require_once("../helpers/db.php");
if (!isset($_SESSION["owner"])){
    header("Location: login.php");
}

if (isset($_POST["username"])){
    if (isset($_POST["addstaff"])){
        $stmt = $conn->prepare("INSERT INTO `staff` (`username`, `password`, `permissions`) VALUES (?, ?, ?)");
        if ($_POST["addstaff"] == "admin"){
            $staff_password = null;
        }
        elseif($_POST["addstaff"] == "owner"){
            $staff_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        }
        $stmt->bind_param("sss", $_POST["username"], $staff_password, $_POST["addstaff"]);
        if ($stmt->execute() !== true) {
            die("Error writing staff info: " . $conn->error);
        }
    }
    elseif(isset($_POST["removestaff"])){
        if ($_POST["removestaff"] == "admin"){
            $staff_password = null;
        }
        elseif($_POST["removestaff"] == "owner"){
            $staff_password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        }
        $stmt = $conn->prepare("DELETE FROM `staff` WHERE username=?");
        $stmt->bind_param("ss", $_POST["username"]);
        if ($stmt->execute() !== true) {
            die("Error deleting staff info: " . $conn->error);
        }
    }
    header('Location: dashboard.php');
}

$ownerinfo = $_SESSION["ownerinfo"];
$stmt = $conn->prepare("SELECT id, username, permissions FROM staff");
$stmt->execute();
$result = $stmt->get_result();
$staff_values = array();
$staff_fields = mysqli_fetch_fields($result);
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row["id"];
    $staff_values[$id] = array();
    foreach ($row as $field => $value) {
        $staff_values[$id][] = $value;
    }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard dueño</title>
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
  </head>
  <body>
    <section class="hero is-primary">
        <div class="hero-body">
          <div class="container">
            <h1 class="title">
              Bienvenido, <?php echo($ownerinfo["username"]);?>
            </h1>
            <h2 class="subtitle">
              Panel de control del dueño
            </h2>
          </div>
        </div>
    </section>
    <section class="section">
        <div class="columns">
            <div class="column">
                <h1 class="title">Staff</h1>
                <table class="table is-bordered is-striped is-hoverable">
                <thead>
                    <tr>
                        <?php
                        // Get all fields from teachers' table
                        foreach($staff_fields as $val){
                            echo ("<th>$val->name</th>");
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($staff_values as $individual){
                        echo <<<EOL
                        <tr>
                        <td>$individual[0]</td>
                        <td>$individual[1]</td>
                        <td>$individual[2]</td>
                        </tr>
                        EOL;
                    }
                    ?>
                </tbody>
                </table>
            </div>
        </div>
        <hr>
        <h1 class="title">Administrar</h1>
        <div class="buttons">
            <button id="managestaff" type="button" class="button">Agregar/eliminar staff</button>
        </div>
    </section>
    <div id="modalstaff" class="modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Agregar staff</p>
                <button class="delete" aria-label="close"></button>
            </header>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                <section class="modal-card-body">
                    <input class="input" type="text" name="username" placeholder="Nombre de usuario" required>
                    <input class="input is-hidden" type="text" id="password" name="password" placeholder="Contraseña">
                    <div class="select">
                        <select id="typestaff">
                            <option value="admin">Administrador</option>
                            <option value="owner">Dueño</option>
                        </select>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button id="addstaff" name="addstaff" value="admin" class="button is-success" type="submit">Agregar</button>
                    <button id="removestaff" name="removestaff" value="admin" class="button is-danger" type="submit">Eliminar</button>
                </footer>
            </form>
        </div>
    </div>
    <footer class="footer">
        <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
            <ul>
                <li>
                    <a href="../logout.php">
                        <span class="icon is-small">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        </span>
                        <span>Cerrar sesión</span>
                    </a>
                </li>
            </ul>
        </nav>
    </footer>
    <script src="scripts/dashboard.js"></script>
  </body>
</html>