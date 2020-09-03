<?php
// Initialize the session
session_start();
require_once("../helpers/db.php");
require_once("../helpers/config.php");
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("Location: ../login.php");
    exit;
}
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

        foreach($files as $file){
            delete_files($file);
        }

        rmdir($target);
    } elseif(is_file($target)) {
        unlink($target);  
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userinfo = $_SESSION["userinfo"];
    $baseurl = $uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/";
    $gallery_dir = 'gallery/';
    $allowed_pic = array('gif', 'png', 'jpg', 'jpeg');
    $allowed_vid = array('mp4', "webm");

    // The user wants to overwrite data
    if(isset($_POST["overwrite"])){
        $stmt = $conn->prepare("DELETE FROM gallery WHERE schoolid=? AND schoolyear=?");
        $stmt->bind_param("is", $userinfo["idcentro"], $userinfo["yearuser"]);
        if ($stmt->execute() !== TRUE) {
            die("Error deleting gallery data: " . $conn->error);
        }
        delete_files($uploadpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/gallery/");
    }
    
    if (!is_dir($baseurl.$gallery_dir)){
        mkdir($baseurl.$gallery_dir, 0700, true);
    }
    if(count($_FILES['pic']['name']) > 0){
        for($i=0; $i<count($_FILES['pic']['name']); $i++) {
            $tmpFilePath = $_FILES['pic']['tmp_name'][$i];
            if($tmpFilePath != ""){
                $filePath = $baseurl.$gallery_dir.$_FILES['pic']['name'][$i];
                $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed_pic)) {
                    echo 'error';
                }
                else{
                    $pictures[$i]["path"] = basename($filePath);
                    $pictures[$i]["description"] = htmlspecialchars($_POST["pic_description"][$i]);
                    $pictures[$i]["type"] = "picture";
                    move_uploaded_file($tmpFilePath, $filePath);
                }
            }
        }
    }

    if(count($_FILES['vid']['name']) > 0){
        for($i=0; $i<count($_FILES['vid']['name']); $i++) {
            $tmpFilePath = $_FILES['vid']['tmp_name'][$i];
            if($tmpFilePath != ""){
                $filePath = $baseurl.$gallery_dir.$_FILES['vid']['name'][$i];
                $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed_vid)) {
                    echo 'error';
                }
                else{
                    $videos[$i]["path"] = basename($filePath);
                    $videos[$i]["description"] = htmlspecialchars($_POST["vid_description"][$i]);
                    $videos[$i]["type"] = "video";
                    move_uploaded_file($tmpFilePath, $filePath);
                }
            }
        }
    }
    // Insert to DB
    // Create row with user data
    if (isset($videos) || isset($pictures)) {
        $stmt = $conn->prepare("insert ignore into gallery(name, schoolid, schoolyear, description, type) VALUES (?, ?, ?, ?, ?)");

        if (isset($pictures)) {
            foreach ($pictures as $pic) {
                $stmt->bind_param("sisss", $pic["path"], $userinfo["idcentro"], $userinfo["yearuser"], $pic["description"], $pic["type"]);
                if ($stmt->execute() !== TRUE) {
                    die("Error inserting pic data: " . $conn->error);
                }
            }
        }

        if (isset($videos)) {
            foreach ($videos as $video) {
                $stmt->bind_param("sisss", $video["path"], $userinfo["idcentro"], $userinfo["yearuser"], $video["description"], $video["type"]);
                if ($stmt->execute() !== TRUE) {
                    die("Error inserting vid data: " . $conn->error);
                }
            }
        }
        $stmt->close();
        // Done
        header("Location: dashboard.php");
        exit;
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galería</title>
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
</head>
<body>
    <section class="hero is-primary is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    Galería
                </h1>
            </div>
        </div>
    </section>
    <section id="gallery_basic" class="section">
        <div class="control has-text-centered">
            <button id="addpic" class="button is-info" type="button">Agregar foto</button>
        </div>
        <br>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
            <div id="pic_columns" class="columns is-centered is-multiline">
                <div class="column is-narrow">
                    <div class="card">
                        <div class="card-content">
                            <p class="title has-text-centered">Foto 0</p>
                            <div class="field">
                                <p class="control">
                                    <label>Foto: </label>
                                    <input type="file" name="pic[]" accept="image/gif, image/jpeg, image/png" multiple="multiple">
                                    <br>
                                    <label for="pic_description[]">Descripción: </label>
                                    <textarea class="textarea" name="pic_description[]" rows="10" cols="30"></textarea>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="control has-text-centered">
                <button id="addvid" class="button is-info" type="button">Agregar vídeo</button>
            </div>
            <br>
            <div id="vid_columns" class="columns is-centered is-multiline">
                <div class="column is-narrow">
                    <div class="card">
                        <div class="card-content">
                            <p class="title has-text-centered">Vídeo 0</p>
                            <div class="field">
                                <p class="control">
                                    <label>Vídeo: </label>
                                    <input type="file" name="vid[]" accept="video/mp4, video/webm" multiple="multiple">
                                    <br>
                                    <label for="vid_description[]">Descripción: </label>
                                    <textarea class="textarea" name="vid_description[]" rows="10" cols="30"></textarea>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <label class="checkbox">
                        <input name="overwrite" type="checkbox">
                        Sobrescribir datos ya existentes
                    </label>
                </div>
            </div>
            <div class="field is-grouped is-grouped-centered">
                <p class="control">
                    <button type="submit" class="button is-primary">Enviar</button>
                </p>
                <p class="control">
                    <a href="dashboard.php" class="button is-danger">Cancelar</a>
                </p>
            </div>
        </form>
    </section>
    <script src="../assets/scripts/admins/gallery.js"></script>
</body>

</html>
