<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

$ownerinfo = $_SESSION["ownerinfo"];

// Get users
require_once("getstaff.php");
require_once("getschools.php");
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
        <div class="columns is-mobile is-centered">
            <div class="column is-narrow">
                <p class="title is-4">
                    <i class="fas fa-user-shield"></i>
                    <span>Staff</span>
                </p>
                <table class="table is-bordered is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre de usuario</th>
                            <th>Permisos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($staff as $user){
                            echo "
                            <tr>
                            <td>$user[0]</td>
                            <td>$user[1]</td>
                            <td>$user[2]</td>
                            </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
                <a href="staff.php" class="button is-info">
                    <span class="icon">
                        <i class="fas fa-user-friends"></i>
                    </span>
                    <span>Agregar/eliminar staff</span>
                </a>
            </div>
            <div class="column is-narrow">
                <p class="title is-4">
                    <i class="fas fa-school"></i>
                    <span>Centros</span>
                </p>
                <table class="table is-bordered is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>URL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($schools as $school){
                            echo "
                            <tr>
                                <td>$school[id]</td>
                                <td>$school[url]</td>
                            </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
                <button id="manageschool" type="button" class="button is-info">
                    <span class="icon">
                        <i class="fas fa-school"></i>
                    </span>
                    <span>Agregar/eliminar centro</span>
                </button> 
            </div>
        </div>
        <hr>
        <p class="title has-text-centered">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Danger zone</span>
        </p>
        <div class="buttons is-centered"> 
            <button id="clear" type="button" class="button is-danger">
                <span class="icon">
                    <i class="fas fa-archive"></i>
                </span>
                <span>Limpiar carpeta de subidas</span>
            </button>  
        </div>
    </section>
    <!-- Modal for adding/removing schools -->
    <div id="modalschool" class="modal">
        <div onclick="closeschool()" class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Agregar/eliminar staff</p>
                <button onclick="closeschool()" class="delete" aria-label="close"></button>
            </header>
            <form action="manageschool.php" method="POST">
                <section class="modal-card-body">
                    <div class="field">
                        <label class="label">Código del centro</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="id" placeholder="124363123" required>
                            <span class="icon is-left">
                                <i class="fas fa-school"></i>
                            </span>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">URL del centro (Opcional y Sólo necesario si estás <b>agregando</b> un centro)</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="schoolurl" placeholder="http://example.com">
                            <span class="icon is-left">
                                <i class="fas fa-link"></i>
                            </span>
                        </div>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button name="addschool" class="button is-success" type="submit">
                        <span class="icon">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span>Agregar</span>
                    </button>
                    <button name="removeschool" class="button is-danger" type="submit">
                        <span class="icon">
                            <i class="fas fa-minus"></i>
                        </span>
                        <span>Eliminar</span>
                    </button>
                </footer>
            </form>
        </div>
    </div>
    <!-- Modal for deleting rows of specific school -->
    <div id="modalclear" class="modal">
        <div onclick="closeclear()" class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Limpiar datos subida</p>
                <button onclick="closeclear()" class="delete" aria-label="close"></button>
            </header>
            <form action="clear.php" method="POST">
                <section class="modal-card-body">
                    <p>Limpia la carpeta de subida</p>
                    <p>
                        <span class="has-background-danger"><strong>ADVERTENCIA</strong></span>,
                        esta función elimina todos los datos subidos por los usuarios.
                        Esta acción no borra las orlas ya generadas.
                        <span class="has-background-danger"><u>ESTA ACCIÓN ES IRREVERSIBLE</u></span>
                    </p>
                    <div class="field">
                        <label class="label">Código del centro</label>
                        <div class="control has-icons-left">
                            <input class="input" type="text" name="id" placeholder="124363123" required>
                            <span class="icon is-left">
                                <i class="fas fa-school"></i>
                            </span>
                        </div>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button name="clear" class="button is-danger" type="submit">
                        <span class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <span>Limpiar</span>
                    </button>
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
    <script src="../assets/scripts/owner/dashboard.js"></script>
  </body>

</html>
