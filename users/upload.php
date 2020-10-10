<?php
session_start();
$enabled = true;
if(!isset($_SESSION["loggedin"])){
    header("Location: ../login.php");
    exit;
}

if (!$enabled) {
    die("El sistema de subida no está activo");
}

require_once("../helpers/db/db.php");
require_once("../helpers/config.php");

$userinfo = $_SESSION["userinfo"];
$db = new DB;

function executestmt($stmt) {
    if ($stmt->execute() !== true) {
        die("Error inserting user data: " . $db->error);
    }
    $stmt->close();
}

switch ($userinfo["typeuser"]) {
    case "ALU":
        $typeuser = "students";
    break;
    case "P":
        $typeuser = "teachers";
    break;
    default:
        die("Ese usuario no es válido");
}

$max_mb = min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'));
$max_characters = 100; // "Quote" max characters
$pic_error = $vid_error = $general_error = "";

// Get what user didn't upload yet
$remain = [];
$stmt = $db->prepare("SELECT photo, video, link, quote FROM $typeuser WHERE schoolid=? AND schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1){
    while ($row = mysqli_fetch_assoc($result)) {
        foreach ($row as $field => $value) {
            if (!$value) {
                array_push($remain, $field);
            }
        }
    }
}
else {
    $remain = ["photo", "video", "link", "quote"];
}
$stmt->close();

if (empty($remain)) {
    die("Ya has subido todos los datos");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare
    // Allowed formats
    $allowed_pic = array('gif', 'png', 'jpg', 'jpeg');
    $allowed_vid = array('mp4', 'webm');
    // Upload directory
    $baseurl = $uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/$typeuser/";

    // Pic upload
    if (in_array("photo", $remain)) {
        if(isset($_FILES['pic'])){
            $tmpFilePath = $_FILES['pic']['tmp_name'];
            if($tmpFilePath != ""){
                $picPath = $baseurl.$userinfo["iduser"]."/".$_FILES['pic']['name'];
                $ext = pathinfo($picPath, PATHINFO_EXTENSION);
                // If the extension is not in the array create error message
                if (!in_array($ext, $allowed_pic)) {
                    $pic_error = "$ext no es un formato admitido.<br>";
                }
                else{
                    if (!is_dir($baseurl.$userinfo["iduser"])){
                        mkdir($baseurl.$userinfo["iduser"], 0700, true);
                    }
                    $picname = basename($picPath);
                    move_uploaded_file($tmpFilePath, $picPath);
                    $stmt = $db->prepare("UPDATE $typeuser SET photo = ? WHERE id=?");
                    $stmt->bind_param("ss", $_FILES["pic"]["name"], $userinfo["iduser"]);
                    executestmt($stmt);
                }
            }
        }
        else {
            $pic_error = "Foto: Ha habido un error al procesar tu solicitud, quizás excediste el límite permitido<br>";
        }
    }

    // Vid upload
    if (in_array("video", $remain)) {
        if(isset($_FILES['vid'])){
            $tmpFilePath = $_FILES['vid']['tmp_name'];
            if($tmpFilePath != ""){
                $vidPath = $baseurl.$userinfo["iduser"]."/".$_FILES['vid']['name'];
                $ext = pathinfo($vidPath, PATHINFO_EXTENSION);
                // If the extension is not in the array create error message
                if (!in_array($ext, $allowed_vid)) {
                    $vid_error = "$ext no es un formato admitido.<br>";
                }
                else{
                    if (!is_dir($baseurl.$userinfo["iduser"])){
                        mkdir($baseurl.$userinfo["iduser"], 0700, true);
                    }
                    $vidname = basename($vidPath);
                    move_uploaded_file($tmpFilePath, $vidPath);
                    $stmt = $db->prepare("UPDATE $typeuser SET video = ? WHERE id=?");
                    $stmt->bind_param("ss", $_FILES["vid"]["name"], $userinfo["iduser"]);
                    executestmt($stmt);
                }
            }
        }
        else {
            $vid_error = "Video: Ha habido un error al procesar tu solicitud, quizás excediste el límite permitido<br>";
        }
    }

    if (in_array("link", $remain)) {
        $link = $_POST["link"];
        $stmt = $db->prepare("UPDATE $typeuser SET link = ? WHERE id=?");
        $stmt->bind_param("ss", $link, $userinfo["iduser"]);
        executestmt($stmt);
    }

    if (in_array("quote", $remain)) {
        if (strlen($_POST["quote"]) > $max_characters) {
            $general_error = "Has excedido la máxima cantidad de caracteres";
        }
        else {
            $quote = nl2br(htmlspecialchars($_POST["quote"]));
            $stmt = $db->prepare("UPDATE $typeuser SET quote = ? WHERE id=?");
            $stmt->bind_param("ss", $quote, $userinfo["iduser"]);
            executestmt($stmt);
        }
    }
    // Reset reason to NULL
    $stmt = $db->prepare("UPDATE $typeuser SET reason = NULL WHERE id=?");
    $stmt->bind_param("s", $userinfo["iduser"]);
    executestmt($stmt);

    header("Location: finish.html");
    exit;
}
?>
<!DOCTYPE html>
<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Subir - IberbookEdu</title>
        <script defer src="https://use.fontawesome.com/releases/v5.9.0/js/all.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css"/>
        <script>
            const remain = <?php echo(json_encode($remain));?>;
        </script>
    </head>

    <body>
        <section class="hero is-primary is-bold">
            <div class="hero-body has-text-centered">
                <figure class="image container is-64x64">
                    <img src="data:image/png;base64, <?php echo($userinfo["photouser"]);?>" alt="Foto Perfil">
                </figure>
                <br>
                <div class="container">
                    <h1 class="title">
                        <?php echo($userinfo["nameuser"]);?>
                    </h1>
                    <h2 class="subtitle">
                        <?php echo($userinfo["yearuser"]);?>
                    </h2>
                </div>
            </div>
        </section>
        <section id="upload" class="section">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
                <div class="container has-text-centered">
                    <p class="title">Subir</p>
                    <div id="photo" class="field animate__animated animate__fadeIn is-hidden">
                        <div id="pic-file" class="file has-name is-centered">
                            <label class="file-label">
                                <input class="file-input" type="file" name="pic" accept="image/gif,image/png,image/jpeg" multiple="multiple">
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fas fa-upload"></i>
                                    </span>
                                    <span class="file-label">
                                        Elige una foto
                                    </span>
                                </span>
                                <span class="file-name">
                                    Ningún archivo elegido
                                </span>
                            </label>
                        </div>
                    </div>
                    <div id="video" class="field animate__animated animate__fadeIn is-hidden">
                        <div id="vid-file" class="file has-name is-centered">
                            <label class="file-label">
                                <input class="file-input" type="file" name="vid" accept="video/mp4,video/webm" multiple="multiple">
                                <span class="file-cta">
                                    <span class="file-icon">
                                        <i class="fas fa-upload"></i>
                                    </span>
                                    <span class="file-label">
                                        Elige un vídeo
                                    </span>
                                </span>
                                <span class="file-name">
                                    Ningún archivo elegido
                                </span>
                            </label>
                        </div>
                    </div>
                    <div id="link" class="field animate__animated animate__fadeIn is-hidden">
                        <label class="label">(Opcional) Enlace</label>
                        <div class="control">
                            <input name="link" class="input" type="text" placeholder="https://github.com/pablouser1/IberbookEdu">
                        </div>
                    </div>
                    <div id="quote" class="field animate__animated animate__fadeIn is-hidden">
                        <label class="label">(Opcional) Cita - Máximo 100 caracteres</label>
                        <div class="control">
                            <textarea id="quote" name="quote" class="textarea" placeholder="¡Hola!" rows="3" maxlength="100"></textarea>
                        </div>
                        <p>
                            <span id="remain_characters">100</span>
                            <span> de 100 caracteres restantes</span>
                        </p>
                    </div>
                    <div class="field">
                        <div class="control">
                            <div class="buttons is-centered">
                                <a class="button is-danger" href="dashboard.php">
                                    <span class="icon">
                                        <i class="fas fa-ban"></i>
                                    </span>
                                    <span>Cancelar</span>
                                </a>
                                <button id="media_submit" type="submit" name="media_form" class="button is-primary">
                                    <span class="icon is-small">
                                        <i class="fas fa-paper-plane"></i>
                                    </span>
                                    <span>Enviar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <progress id="upload_progress" class="progress is-primary is-hidden" max="100"></progress>
                    <hr>
                    <?php
                    if ($pic_error || $vid_error || $general_error !== ""){
                        echo "
                        <div class='notification is-danger'>
                            $general_error
                            $pic_error
                            $vid_error
                        </div>
                        ";
                    }
                    ?>
                    <div class="notification is-info">
                        <b>Formatos admitidos:</b><br>
                        Fotos: gif, png, jpg, jpeg.<br>
                        Vídeos: mp4, webm.<br>
                        <span class="has-background-danger">Tamaño máximo <?php echo($max_mb);?> MB</span>
                    </div>
                </div>
            </form>
        </section>
        <script src="../assets/scripts/users/upload.js"></script>
    </body>
</html>
