<?php
session_start();
if(!isset($_SESSION["loggedin"], $_SESSION["userinfo"])){
    header("Location: ../login.php");
    exit;
}
require_once("../helpers/db.php");
require_once("../helpers/config.php");

$userinfo = $_SESSION["userinfo"];
$yearuser = str_replace(' ', '', $userinfo["yearuser"]);
$table_name = $_SESSION["table_name"];
$max_mb = min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'));
$max_characters = 280; // "Quote" max characters
$pic_error = $vid_error = $general_error = "";

// Check if user uploaded pic and vid before
$stmt = $conn->prepare("SELECT id FROM $table_name where schoolid=? and schoolyear=?");
$stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1){
    die("No tienes permisos para acceder a esta página ya que ya has subido tus datos.");
}
$stmt->close();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare
    // Allowed formats
    $allowed_pic = array('gif', 'png', 'jpg', 'jpeg');
    $allowed_vid = array('mp4', 'webm');
    // Upload directory
    $baseurl = $uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/$table_name/";
    if(strlen($_POST["quote"]) > $max_characters){
        $general_error = "Has excedido la máxima cantidad de caracteres";   
    }
    // Continue with the files upload if the quote didn't excede the max amount
    else{
        // Pic upload
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
                }
            }
        }
        else{
            $general_error = "Ha habido un error al procesar la solicitud, quizás alguno de los archivos pesa más de lo aceptado<br>";
        }
        // Vid upload
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
                }
            }
        }
        else{
            $general_error = "Ha habido un error al procesar la solicitud, quizás alguno de los archivos pesa más de lo aceptado<br>";
        }
    }
    // Inject to DB
    if (empty($vid_error) && empty($pic_error) && empty($general_error)){
        $qoute = ($_POST["quote"]) ? nl2br(htmlspecialchars($_POST["quote"])) : null;
        // Create row with user data, nl2br is used to follow line breaks.
        if ($userinfo["typeuser"] == "P"){
            $stmt = $conn->prepare("INSERT INTO $table_name (id, fullname, schoolid, schoolyear, picname, vidname, link, quote, subject) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssissssss",
            $userinfo["iduser"], $userinfo["nameuser"], $userinfo["idcentro"], $userinfo["yearuser"], $picname, $vidname, $_POST["link"], $qoute, $userinfo["subject"]);
        }
        else{
            $stmt = $conn->prepare("INSERT INTO $table_name (id, fullname, schoolid, schoolyear, picname, vidname, link, quote) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisssss", $userinfo["iduser"], $userinfo["nameuser"], $userinfo["idcentro"], $userinfo["yearuser"], $picname, $vidname, $_POST["link"], $qoute);
        }
        if ($stmt->execute() !== true) {
            die("Error inserting user data: " . $conn->error);
        }
        $stmt->close();
        header("Location: finish.html");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Subir - IberbookEdu</title>
        <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css"/>
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
                    <div class="field">
                        <p class="title">Subir</p>
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
                    <div class="field">
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
                    <div class="field">
                        <label class="label">(Opcional) Enlace</label>
                        <div class="control">
                            <input name="link" class="input" type="text" placeholder="https://github.com/pablouser1/IberbookEdu">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">(Opcional) Cita - Máximo 280 caracteres</label>
                        <div class="control">
                            <textarea id="quote" name="quote" class="textarea" placeholder="¡Hola!" rows="3" maxlength="280"></textarea>
                        </div>
                        <p>
                            <span id="remain_characters">280</span>
                            <span> de 280 caracteres restantes</span>
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