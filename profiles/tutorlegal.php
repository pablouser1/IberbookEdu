<?php
session_start();
if (!isset($_SESSION["loggedin"], $_SESSION["tutorinfo"])){
    header("Location: login.php");
    exit;
}
require_once("../helpers/api.php");
$cookies = $_SESSION["cookies"];

$tutorinfo = $_SESSION["tutorinfo"];
// Get pics of children
$picschildren = array();
foreach($tutorinfo["children"] as $id => $child){
    $datapic = array('X_MATRICULA' => $child["MATRICULAS"][0]["X_MATRICULA"], "ANCHO" => 64, "ALTO" => 64);
    $picschildren[$id] = getpicstudent($cookies, $datapic);
}

if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['childlogin'])){
    $child = $tutorinfo["children"][$_POST['childlogin']];
    $datacentro = array("X_CENTRO" => $child["MATRICULAS"][0]["X_CENTRO"]);
    $infocentro = getcentrostudent($cookies, $datacentro);
    $userinfo = array(
        "iduser" => $child["MATRICULAS"][0]["X_MATRICULA"],
        "nameuser" => $child["NOMBRE"],
        "typeuser" => "ALU",
        "yearuser" => $child["MATRICULAS"][0]["UNIDAD"],
        "photouser" => $picschildren[$_POST['childlogin']],
        "idcentro" => $infocentro["idcentro"],
        "namecentro" => $infocentro["namecentro"]
    );
    $_SESSION["userinfo"] = $userinfo;
    header("Location: ../users/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Selección tutor legal</title>
      <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
  </head>
  <body>
    <section class="hero is-primary">
        <div class="hero-body">
          <div class="container">
            <h1 class="title">
              Bienvenido, <?php echo($tutorinfo["nameuser"]);?>
            </h1>
            <h2 class="subtitle">
              Elige tu hijo
            </h2>
          </div>
        </div>
      </section>
    <section class="section">
        <div class="columns is-multiline is-centered is-mobile">
            <?php
            foreach($tutorinfo["children"] as $id => $child){
                echo '
                <div class="column is-narrow is-full-mobile">
                    <div class="card">
                        <div class="card-content">
                            <div class="media">
                                <div class="media-left">
                                    <figure class="image is-64x64">
                                        <img src="data:image/png;base64,'.$picschildren[$id].'" alt="Placeholder image">
                                    </figure>
                                </div>
                                <div class="media-content">
                                    <p class="title is-4">'.$child["NOMBRE"].'</p>
                                    <p class="subtitle is-6">'.$child["MATRICULAS"][0]["UNIDAD"].'</p>
                                </div>
                            </div>
                            <div class="content has-text-centered">
                                <form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post">
                                    <button name="childlogin" value="'.$id.'" class="button is-link">Iniciar sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                ';
            }
            ?>
        </div>
    </section>
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
  </body>
</html>
