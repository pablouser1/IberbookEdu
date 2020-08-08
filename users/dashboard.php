<?php
session_start();
require("../helpers/common.php");
require_once("../helpers/db.php");
if(isset($_SESSION["loggedin"], $_SESSION["userinfo"])){
    // User data
    $userinfo = $_SESSION["userinfo"];
    if ($userinfo["typeuser"] == "P"){
        $table_name = "teachers";
    }
    else{
        $table_name = "students";
    }
    $_SESSION["table_name"] = $table_name;
    // Elimina datos del usuario
    if(isset($_POST['reset'])) {
        // Base de datos
        $stmt = $conn->prepare("DELETE FROM $table_name WHERE id=?");
        $stmt->bind_param("s", $userinfo["iduser"]);
        if ($stmt->execute() !== true) {
            die("Error deleting data: " . $conn->error);
        }
        $stmt->close();
        // Ficheros
        delete_files('../yearbooks/'.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/uploads/".$table_name."/".$userinfo["iduser"]);
        header("Location: dashboard.php");
    }
    // Check if yearbook is available
    $stmt = $conn->prepare("SELECT generated, available FROM yearbooks WHERE schoolid=? AND schoolyear=?");
    $stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($generated, $available);
    if ($stmt->num_rows == 1) {
        if(($result = $stmt->fetch()) == true && $available == 1){
            $yearbook = array(
                "date" => $generated,
                "available" => $available,
            );
        }
    }
    $stmt->close();
    // Check if user uploaded pic and vid before
    if (isset($userinfo["subject"])){
        $stmt = $conn->prepare("SELECT id, fullname, picname, vidname, subject FROM $table_name where schoolid=? and schoolyear=?");
    }
    else{
        $stmt = $conn->prepare("SELECT id, fullname, picname, vidname FROM $table_name where schoolid=? and schoolyear=?");
    }
    $stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
    $stmt->execute();
    $result = $stmt->get_result();
}
else{
    header("Location: ../login.php");
}
?>
<!DOCTYPE html>
<html>
    
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard IberbookEdu</title>
        <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
    </head>

    <body>
        <section class="hero is-primary is-bold">
            <div class="hero-body has-text-centered">
                <figure class="image container is-64x64">
                    <img src="data:image/png;base64, <?php echo($userinfo["photouser"]);?>" alt="Foto Perfil">
                </figure>
                <br>
                <div class="container">
                    <h1 class="title"><?php echo($userinfo["nameuser"]);?></h1>
                    <h2 class="subtitle"><?php echo($userinfo["yearuser"]);?></h2>
                </div>
            </div>
        </section>
        <section id="dashboard" class="section">
            <?php
            if(isset($yearbook)){
                echo <<<END
                <section class="hero is-medium is-success is-bold">
                    <div class="hero-body">
                        <div class="container">
                            <h1 class="title">
                                Tu yearbook está listo
                            </h1>
                            <p class="subtitle">
                                <a href="../download.php" class="button is-success">
                                    <span class="icon">
                                        <i class="fas fa-download"></i>
                                    </span>
                                    <span>Descargar</span>
                                </a>
                            </p>
                        </div>
                    </div>
                </section>
                <hr>
                END;
            }
            if ($result->num_rows == 0){
                echo <<<END
                <div class="content has-text-centered">
                    <h1 class="title">¡Hola! Bienvenido</h1>
                    <p>Parece ser que no tienes ninguna foto o vídeo subido, puedes comenzar pulsando el botón:</p>
                    <a class="button is-info" href="upload.php">
                        <span class="icon">
                            <i class="far fa-images"></i>
                        </span>
                        <span>Agregar foto y vídeo</span>
                    </a>
                </div>
            END;
            }
            elseif ($result->num_rows == 1) {
                $user_values = array();
                $user_fields = mysqli_fetch_fields($result);
                while ($row = mysqli_fetch_assoc($result)) {
                    $id = $row["id"];
                    $user_values[$id] = array();
                    foreach ($row as $field => $value) {
                        $user_values[$id][] = $value;
                    }
                }
                echo("<h1 class='title'>Tus datos</h1>");
            }
            ?>
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable">
                    <thead>
                        <tr>
                            <?php
                            // Get all fields from user's table
                            if(!empty($user_fields)) {
                                foreach($user_fields as $val){
                                    echo ("<th>$val->name</th>");
                                }
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get all values from user' table
                        if (!empty($user_values)) {
                            foreach($user_values as $value => $individual){
                                echo <<<END
                                <tr>
                                    <td>$individual[0]</td>
                                    <td>$individual[1]</td>
                                    <td><a href='../uploads/$userinfo[idcentro]/$userinfo[yearuser]/$table_name/$individual[0]/$individual[2]' 
                                    target='_blank'>$individual[2]</a></td>
                                    <td><a href='../uploads/$userinfo[idcentro]/$userinfo[yearuser]/$table_name/$individual[0]/$individual[3]' 
                                    target='_blank'>$individual[3]</a></td>
                                END;
                                if($userinfo["typeuser"] == "P"){
                                    echo("<td>$individual[4]</td>");
                                }
                                echo("</tr>");
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
            // Comprobar si ya hay información subida
            if ($result->num_rows == 1 && !isset($yearbook)){
                echo '
                <hr>
                <form method="post">
                    <button id="delete_button" class="button is-danger" type="button">
                        <span class="icon">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Eliminar datos</span>
                    </button>
                </form>
                ';
            }
            ?>
        </section>
        <div id="delete_modal" class="modal">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">¿Seguro?</p>
                </header>
                <section class="modal-card-body">
                    <p>Al eliminar los datos tendrás que <b>volver a subir tus datos otra vez</b></p>
                </section>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <footer class="modal-card-foot">
                        <button name="reset" type="submit" class="button">Continuar</button>
                        <button id="delete_cancel" type="button" class="button">Cancelar</button>
                    </footer>
                </form>
            </div>
        </div>
        <script src="../assets/scripts/users/dashboard.js"></script>
        <footer class="footer">
            <div class="content has-text-centered">
                <a href="../logout.php">Cerrar sesión</a>
            </div>
        </footer>
    </body>
</html>
