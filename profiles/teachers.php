<?php
session_start();
if (!isset($_SESSION["loggedin"]) && !isset($_SESSION["teacherinfo"])){
    header("Location: ../login.php");
    exit;
}

require_once("../helpers/api.php");

$teacherinfo = $_SESSION["teacherinfo"];

// User submitted info
if (isset($_GET["select_curso"], $_GET["schoolid"])){
  $valid = array();
  // School id and group id
  $schoolid = (int)$_GET["schoolid"];
  $groupid = $_GET["select_curso"];
  $finalschools = $teacherinfo["schools"];
  // Check if user didn't manipulate input
  if(isset($finalschools[$schoolid]["groups"][$groupid])) {
    $group = $finalschools[$schoolid]["groups"][$groupid]["name"];
    $subject = $finalschools[$schoolid]["groups"][$groupid]["subject"];
    $valid["groupinfo"] = true;
  }
  
  if($_GET["schoolid"] == $finalschools[$schoolid]["id"]){
    $schoolname = $finalschools[$schoolid]["name"];
    $valid["schoolid"] = true;
  }

  // If everything is OK, continue
  if(count($valid) === 2){
    $userinfo = array(
      "iduser" => $teacherinfo["iduser"],
      "nameuser" => $teacherinfo["nameuser"],
      "typeuser" => $teacherinfo["typeuser"],
      "subject" => $subject,
      "yearuser" => $group,
      "photouser" => base64_encode(file_get_contents("../assets/img/PortraitPlaceholder.png")), // SENECA doesn't have photos
      "idcentro" => $_GET["schoolid"],
      "namecentro" => $schoolname
    );

    require_once("../helpers/createprofile.php");
    // Create profile
    $profile = createprofile($userinfo, "teachers");
    $userinfo["id"] = $profile;
    $_SESSION["userinfo"] = $userinfo;
    header("Location: ../index.php");
    exit;
  }
  else {
    $errors[] = "Ha habido un error al procesar tu solicitud";
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selección profesores - IberbookEdu</title>
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
  </head>
  <body>
    <section class="hero is-primary">
      <div class="hero-body">
        <div class="container">
          <h1 class="title">
            Bienvenido, <?php echo($teacherinfo["nameuser"]);?>
          </h1>
          <h2 class="subtitle">
            Elige tu centro y tu curso
          </h2>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="columns is-mobile is-multiline">
        <?php
        if (empty($teacherinfo["schools"])){
          echo '
          <div class="column">
            <p class="title">No tienes centros aceptados</p>
          </div>
          ';
        }
        else {
          foreach($teacherinfo["schools"] as $id => $school){
            echo '
            <div class="column">
              <div class="card">
                <header class="card-header">
                  <p class="card-header-title">
                    '.$school["name"].'
                  </p>
                </header>
                <div class="card-content">
                  <p class="subtitle">Selecciona un curso</p>
                  <form method="get" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">
                    <input name="schoolid" type="hidden" value="'.$school["id"].'"></input>
                    <div class="field">
                      <div class="control">
                        <div class="select">
                          <select name="select_curso">';
                          foreach($school["groups"] as $id => $group){
                            if(!empty($group)){
                              echo '
                              <option value='.$id.'>'.$group["name"].' - '.$group["subject"].'</option>
                              ';
                            }
                            else echo '<option value="notavailable">No hay grupos disponibles</option>';
                          }
                          echo '
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="field">
                      <div class="control">
                        <button id="login_teacher" class="button is-primary">Continuar</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>';
          }
        }
        ?>
      </div>
      <progress id="progress" class="progress is-primary is-hidden" max="100"></progress>
      <div class="container <?php if(!isset($errors)) echo("is-hidden");?>">
        <div class="notification is-danger">
          <?php
          if(isset($errors)){
            foreach ($errors as $error) {
              echo($error);
            }
          }
          ?>
        </div>
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
