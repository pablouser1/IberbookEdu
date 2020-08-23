<?php
session_start();
if (!isset($_SESSION["loggedin"]) && !isset($_SESSION["teacherinfo"])){
    header("Location: ../login.php");
}
$finalschools = array();
require_once("../helpers/api.php");
require_once("../helpers/db.php");
$teacherinfo = $_SESSION["teacherinfo"];
// Get groups
$cookies = $_SESSION["cookies"];
// Set array with only allowed schools
$stmt = $conn->prepare("SELECT name, id FROM schools WHERE id=?");
// Check each school available
foreach($teacherinfo["centros"] as $id => $centro){
  $stmt->bind_param("i", $centro["id"]);
  $stmt->execute();
  $stmt->store_result();
  $stmt->bind_result($allowed_name, $allowed_id);
  if($stmt->num_rows == 1) {
    if(!empty($centro["X_CENTRO"])){
      $data = ["X_CENTRO" => $centro["X_CENTRO"], "C_PERFIL" => "P"];
      changeschoolteachers($cookies, $data);
    }
    $groups = getgroupsteachers($cookies);
    while ($stmt->fetch()) {
      // Set basic school info
      $finalschools[$id] = [
        "name" => $allowed_name,
        "id" => $allowed_id,
      ];
    }
    // Set groups info
    if(empty($groups)){
      $finalschools[$id]["groups"][] = [];
    }
    else {
      foreach ($groups as $group) {
        $finalschools[$id]["groups"][] = $group;
      }
    }
  }
}
$stmt->close();
// User submitted info
if (isset($_GET["select_curso"], $_GET["schoolname"], $_GET["schoolid"])){
  // https://stackoverflow.com/a/4128377
  function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }
  
    return false;
  }
  if($_GET["select_curso"] !== "notavailable") {
    $valid["select_ok"] = true;
    $groupform = explode("-", $_GET["select_curso"]);
    $id = $_GET["schoolid"];
    // Check if user didn't manipulate input
    if(in_array_r($groupform[0], $finalschools[$id])){
      $valid["groupname"] = true;
    }
    if(in_array_r($groupform[1], $finalschools[$id])){
      $valid["subjectname"] = true;
    }
    if($_GET["schoolname"] == $finalschools[$id]["name"]){
      $valid["schoolname"] = true;
    }
    if($_GET["schoolid"] == $finalschools[$id]["id"]){
      $valid["schoolid"] = true;
    }
    // If everything is OK, continue
    if(isset($valid["select_ok"], $valid["groupname"], $valid["subjectname"], $valid["schoolname"], $valid["schoolid"])){
      $userinfo = array(
        "iduser" => $teacherinfo["iduser"],
        "nameuser" => $teacherinfo["nameuser"],
        "typeuser" => $teacherinfo["typeuser"],
        "subject" => $groupform[1],
        "yearuser" => $groupform[0],
        "photouser" => base64_encode(file_get_contents("../assets/img/PortraitPlaceholder.png")), // SENECA doesn't have photos
        "idcentro" => $_GET["schoolid"],
        "namecentro" => $_GET["schoolname"]
      );
      $_SESSION["userinfo"] = $userinfo;
      header("Location: ../users/dashboard.php");
    }
    else {
      $error[] = "Ha habido un error al procesar tu solicitud, ¿has modificado los valores?";
    }
  }
  else {
    $error[] = "Ese centro no tiene cursos disponibles";
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Selección profesores - IberbookEdu</title>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
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
            if (empty($finalschools)){
              echo '
              <div class="column">
                <p class="title">No tienes centros aceptados</p>
              </div>
              ';
            }
            else {
              foreach($finalschools as $id => $school){
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
                        <input name="schoolname" type="hidden" value="'.$school["name"].'"></input>
                        <div class="field">
                          <div class="control">
                            <div class="select">
                              <select name="select_curso">
                              ';
                              foreach($school["groups"] as $group){
                                if(!empty($group)){
                                  echo '
                                  <option value="'.$group["name"].'-'.$group["subject"].'">'.$group["name"].' - '.$group["subject"].'</option>
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
                </div>
                ';
              }
            }
            ?>
        </div>
        <progress id="progress" class="progress is-primary is-hidden" max="100"></progress>
        <div class="container <?php if(!isset($error)) echo("is-hidden");?>">
          <div class="notification is-danger">
            <?php
            if(isset($error)){
              foreach ($error as $error_i) {
                echo($error_i);
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
    <script src="../assets/scripts/profiles/teachers.js"></script>
  </body>
</html>
