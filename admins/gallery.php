<?php
// Initialize the session
session_start();
require_once("../helpers/db.php");
require_once("../helpers/config.php");
require("../helpers/common.php");
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== "admin"){
    header("location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userinfo = $_SESSION["userinfo"];
    $baseurl = $ybpath.$userinfo["idcentro"]."/".$userinfo["yearuser"]."/uploads";
    $gallery_dir = '/gallery/';
    $allowed_pic = array('gif', 'png', 'jpg', 'jpeg');
    if(count($_FILES['gallery']['name']) > 0){
        if (!is_dir($baseurl.$gallery_dir)){
            mkdir($baseurl.$gallery_dir, 0700, true);
        }
        for($i=0; $i<count($_FILES['gallery']['name']); $i++) {
            $tmpFilePath = $_FILES['gallery']['tmp_name'][$i];
            if($tmpFilePath != ""){
                $filePath = $baseurl.$gallery_dir.$_FILES['gallery']['name'][$i];
                $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed_pic)) {
                    echo 'error';
                }
                else{
                    $gallery[] = $i;
                    $gallery[$i] = array();
                    $gallery[$i]["path"] = basename($filePath);
                    $gallery[$i]["description"] = test_input($_POST["gallery_description"][$i]);
                    move_uploaded_file($tmpFilePath, $filePath);
                }
            }
        }
    }
    // Insert to DB
    // Create row with user data
    $stmt = $conn->prepare("insert ignore into gallery(picname, schoolid, schoolyear, picdescription) VALUES (?, ?, ?, ?)");
    foreach ($gallery as $id => $pic) {
        $stmt->bind_param("siss", $pic["path"], $userinfo["idcentro"], $userinfo["yearuser"], $pic["description"]);
        if ($stmt->execute() !== TRUE) {
            die("Error inserting user data: " . $conn->error);
        }
    }
    $stmt->close();
    // Done
    header("Location: dashboard.php");
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
            <div id="gallery_columns" class="columns is-centered is-multiline">
                <div class="column is-narrow">
                    <div class="card">
                        <div class="card-content">
                            <p class="title has-text-centered">Foto 0</p>
                            <div class="field">
                                <p class="control">
                                    <label>Foto: </label>
                                    <input type="file" name="gallery[]" accept="image/*" multiple="multiple">
                                    <br>
                                    <label for="gallery_description[]">Descripción: </label>
                                    <textarea class="textarea" name="gallery_description[]" rows="10" cols="30"></textarea>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="field is-grouped is-grouped-centered">
                <div class="control">
                    <label class="checkbox">
                        <input name="overwrite" type="checkbox">
                        Sobrescribir
                    </label>
                </div>
            </div>
            <div class="field is-grouped is-grouped-centered">
                <p class="control">
                    <button type="submit" class="button is-primary">Enviar</button>
                </p>
                <p class="control">
                    <button type="button" name="cancel_gallery" class="button is-danger">Cancelar</button>
                </p>
            </div>
        </form>
    </section>
    <script src="../assets/scripts/admins/gallery.js"></script>
</body>

</html>