<?php
session_start();
if (!isset($_SESSION["loggedin"]) && !isset($_SESSION["teacherinfo"])){
    header("Location: ../login.php");
}
require_once("../helpers/api.php");
require_once("../helpers/db.php");
$teacherinfo = $_SESSION["teacherinfo"];
// Get only allowed schools
$stmt = $conn->prepare("SELECT name, id FROM schools WHERE id=?");
foreach($teacherinfo["centros"] as $id => $centro){
  $stmt->bind_param("i", $centro["id"]);
  $stmt->execute();
  $stmt->store_result();    
  $stmt->bind_result($allowed_name, $allowed_id);  // <- Add; #args = #cols in SELECT
  if($stmt->num_rows == 1) {
      while ($stmt->fetch()) {
          $allowed_schools[$id]["name"] = $allowed_name;
          $allowed_schools[$id]["id"] = $allowed_id;
      }
  }
}
$stmt->close();
$cookies = $_SESSION["cookies"];
$groups = getgroupsteachers($cookies);

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
  $groupform = explode("-", $_GET["select_curso"]);
  // Check if user didn't manipulate input
  if(in_array_r($groupform[0], $groups)){
    $valid["groupname"] = true;
  }
  if(in_array_r($groupform[1], $groups)){
    $valid["subjectname"] = true;
  }
  if(in_array_r($_GET["schoolname"], $allowed_schools)){
    $valid["schoolname"] = true;
  }
  if(in_array_r($_GET["schoolid"], $allowed_schools)){
    $valid["schoolid"] = true;
  }
  // If everything is OK, continue
  if(isset($valid["groupname"], $valid["subjectname"], $valid["schoolname"], $valid["schoolid"]) || isset($_GET["debug"])){
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
    if($_SESSION["loggedin"] == "admin"){
      header("Location: ../profiles/admin.php");
    }
    else{
      header("Location: ../users/dashboard.php");
    }
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
            // What even is this
            foreach($allowed_schools as $i => $school){
              if (empty($groups)){
                echo '
                <div class="column">
                  <p class="title">'.$school.' no tiene cursos disponibles</p>
                </div>
                ';
              }
              else{
                echo '
                <div class="column">
                    <div class="card">
                        <header class="card-header">
                            <p class="card-header-title">
                            '.$school["name"].'
                            </p>
                        </header>
                        <div class="card-content">
                          <p class="subtitle">Seleciona un curso</p>
                ';
                // Cursos de profesores
                echo('
                <form method="get" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">
                  <input name="schoolid" type="hidden" value="'.$school["id"].'"></input>
                  <input name="schoolname" type="hidden" value="'.$school["name"].'"></input>
                  <div class="field">
                    <div class="control">
                      <div class="select">
                        <select name="select_curso">');
                foreach($groups as $id => $grupo){
                  echo <<<EOL
                      <option value="$grupo[name]-$grupo[subject]">$grupo[name] - $grupo[subject]</option>
                  EOL;
                }
                echo '
                </select></div></div></div>
                <div class="field">
                  <div class="control">
                    <button id="login_teacher" class="button is-primary">Continuar</button>
                  </div>
                </div>
                </form></div></div></div></div>';
              }
            }
            ?>
        </div>
        <progress id="progress" class="progress is-primary is-hidden" max="100"></progress>
        <?php
        if(isset($error)){
          echo <<<EOL
          <div class="container">
            <div class="notification is-danger">
              $error
            </div>
          </div>
          EOL;
        }
        ?>
    </section>
    <script src="../assets/scripts/profiles/teachers.js"></script>
  </body>
</html>