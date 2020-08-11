<?php
session_start();
require_once("../helpers/db.php");
if (!isset($_SESSION["owner"])){
    header("Location: login.php");
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
        <div class="buttons">
            <button id="managestaff" type="button" class="button">Agregar/eliminar staff</button>
        </div>
    </section>
    <hr>
    <div id="modalstaff" class="modal">
        <div onclick="closestaff()" class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Agregar/eliminar staff</p>
                <button onclick="closestaff()" class="delete" aria-label="close"></button>
            </header>
            <form action="managestaff.php" method="POST">
                <section class="modal-card-body">
                    <div class="field">
                        <label class="label">Nombre de usuario</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="username" placeholder="usuario" required>
                            <span class="icon is-left">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                    </div>
                    <div id="password" class="field is-hidden">
                        <label class="label">Contraseña</label>
                        <div class="control has-icons-left">
                            <input class="input" type="password" name="password" placeholder="***********">
                            <span class="icon is-left">
                                <i class="fas fa-key"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Tipo de usuario</label>
                        <div class="control">
                            <div class="select">
                                <select id="typestaff">
                                    <option value="admin">Administrador</option>
                                    <option value="owner">Dueño</option>
                                </select>
                            </div>
                        </div>
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