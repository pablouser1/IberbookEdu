<?php
session_start();
require_once("../helpers/db.php");
ob_start();

// Get vars from before and set a new one
$students = $_SESSION["students"];
$teachers = $_SESSION["teachers"];
$userinfo = $_SESSION["userinfo"];
$gallery = $_SESSION["gallery"];
$baseurl = $_SESSION["baseurl"];
// Copying files
copy("../assets/styles/yearbook/generate.css", $baseurl.'styles/yearbook.css');
copy("../assets/scripts/yearbook/generate.js", $baseurl.'scripts/yearbook.js');
copy("../assets/scripts/yearbook/lang.js", $baseurl.'scripts/lang.js');
copy("../assets/scripts/yearbook/splashscreen.js", $baseurl.'scripts/splashscreen.js');
copy("../assets/scripts/yearbook/vendor/fontawesome.js", $baseurl.'scripts/vendor/fontawesome.js');
copy("../assets/scripts/yearbook/vendor/confetti.min.js", $baseurl.'scripts/vendor/confetti.min.js');
copy("../assets/styles/yearbook/vendor/bulma.min.css", $baseurl.'styles/vendor/bulma.min.css');
copy("../assets/styles/yearbook/vendor/animate.min.css", $baseurl.'styles/vendor/animate.min.css');
copy("externalprojects_licenses.txt", $baseurl.'externalprojects_licenses.txt');
copy("../LICENSE",  $baseurl.'LICENSE.txt');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yearbook <?php echo($userinfo["namecentro"]);?></title>
    <script defer src="scripts/vendor/fontawesome.js"></script>
    <script src="scripts/vendor/confetti.min.js"></script>
    <link rel="stylesheet" href="styles/vendor/bulma.min.css">
    <link rel="stylesheet" href="styles/vendor/animate.min.css"/>
    <link rel="stylesheet" href="styles/yearbook.css">
    <!-- User data in JSON, output of PHP -->
    <script type="text/javascript">
        students_js = <?php echo json_encode($students);?>;
        teachers_js = <?php echo json_encode($teachers);?>;
        gallery_js = <?php echo json_encode($gallery);?>;
        schoolyear_js = '<?php echo($userinfo["yearuser"]);?>';
    </script>
</head>

<body>
    <section id="banner" class="hero is-primary is-hidden">
        <div class="hero-body">
            <div class="container">
                <h1 class="title has-text-centered"><?php echo($userinfo["namecentro"]);?>:</h1>
                <h2 id="hero_subtitle" class="subtitle has-text-centered"></h2>
            </div>
        </div>
    </section>
    <noscript>This program needs Javascript</noscript>
    <section id="loading" class="hero is-fullheight">
        <div id="loading_body" class="hero-body">
            <div class="container">
                <p class="title">
                    Loading...
                </p>
                <p class="subtitle">
                    <progress class="progress is-large is-primary" max="100"></progress>
                </p>
            </div>
        </div>
    </section>
    <div id="tabs" class="tabs is-centered is-hidden">
        <ul id="tabs">
            <li id="tab_yearbook" class="is-active">
                <a onclick="tabchange('yearbook')">
                    <span class="icon is-small"><i class="fas fa-book-open" aria-hidden="true"></i></span>
                    <span>Yearbook</span>
                </a>
            </li>
            <li id="tab_gallery">
                <a onclick="tabchange('gallery')">
                    <span class="icon is-small"><i class="fas fa-image" aria-hidden="true"></i></span>
                    <span id="span_gallery"></span>
                </a>
            </li>
            <li id="tab_about">
                <a onclick="tabchange('about')">
                    <span class="icon is-small"><i class="fas fa-info-circle" aria-hidden="true"></i></span>
                    <span id="span_about"></span>
                </a>
            </li>
        </ul>
    </div>
    <section id="yearbook" class="section is-hidden py-0">
        <!-- Profesores -->
        <p class="title is-4 has-text-centered">
            <i class="fas fa-chalkboard-teacher"></i>
            <span id="teachers_title"></span>
        </p>
        <div class="columns is-mobile is-centered is-multiline is-vcentered">
            <?php
            foreach ($teachers as $id => $teacher) {
                echo <<<EOL
                <div class="column is-half-mobile is-one-third-tablet is-one-fifth-desktop">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image figure_teacher">
                                <img onclick="viewvideo('$id', 'teachers')" src="$teacher[pic]">
                                <figcaption style="top:auto;" class="has-text-centered is-overlay">
                                    <span class="tag">$teacher[subject]</span>
                                </figcaption>
                            </figure>
                        </div>
                        <div class="card-content">
                            <div class="media">
                                <div class="media-content has-text-centered">
                                    <p class="title is-size-4">$teacher[name]</p>
                                    <p class="subtitle is-size-6">$teacher[surnames]</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                EOL;
            }
            ?>
        </div>
        <hr>
        <!-- Alumnos -->
        <p class="title is-4 has-text-centered">
            <i class="fas fa-user-graduate"></i>
            <span id="students_title">Galería</span>
        </p>
        <div class="columns is-mobile is-centered is-multiline is-vcentered">
            <?php
            foreach ($students as $id => $student) {
                echo <<<EOL
                <div class="column is-half-mobile is-one-third-tablet is-one-fifth-desktop">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image figure_student">
                                <img onclick="viewvideo($id, 'students')" src="$student[pic]">
                            </figure>
                        </div>
                        <div class="card-content">
                            <div class="media">
                                <div class="media-content has-text-centered">
                                    <p class="title is-4">$student[name]</p>
                                    <p class="subtitle is-6">$student[surnames]</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                EOL;
            }
            ?>
        </div>
    </section>
    <section id="video_preview" class="hero is-fullheight video is-hidden">
        <div class="hero-video is-block-mobile animate__animated animate__fadeIn">
            <video id="bgvid" playsinline muted controls></video>
        </div>
        <div class="hero-body animate__animated animate__fadeInLeft">
            <div class="title">
                <article class="tile is-child notification is-primary">
                    <p id="video_preview_title" class="title"></p>
                    <p id="video_preview_subtitle" class="subtitle"></p>
                    <button id="video_link" class="button is-link" type="button"></button>
                    <button id="video_exit" class="button is-danger" type="button"></button>
                  </article>
            </div>
        </div>
    </section>
    <section id="gallery" class="section is-hidden">
        <div class="columns is-centered is-multiline">
            <?php
            foreach ($gallery as $id => $pic) {
                echo <<<EOL
                <div class="column is-one-quarter-desktop">
                    <div class="container">
                        <div class="card">
                            <div class="card-content">
                                <figure class="image figure_gallery">
                                    <img onclick="viewphoto('$id')" src="$pic[path]">
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
                EOL;
            }
            ?>
        </div>
        <div id="gallery_modal" class="modal">
            <div onclick="exitphoto()" class="modal-background"></div>
            <div class="modal-card animate__animated animate__fadeIn">
                <header class="modal-card-head">
                    <i class="fas fa-image"></i><p id="gallery_modal_title" class="modal-card-title"></p>
                    <button onclick="exitphoto()" class="modal-close is-large" aria-label="close"></button>
                </header>
                <section class="modal-card-body">
                    <div class="container has-text-centered">
                        <img id="imagen_modal" src="">
                    </div>
                    <br>
                    <div class="container">
                        <p id="gallery_description" class="modal-card-title"></p>
                    </div>
                </section>
                <footer class="modal-card-foot">
                </footer>
            </div>
        </div>
    </section>
    <section id="about" class="section is-hidden">
        <div class="container">
            <p id="about_attribution">
            </p>
            <hr>
            <p id="credits"></p>
            <br>
            <button id="contributors_button" onclick="contributors_open()" class="button is-info" type="button"></button>
            <div id="contributors_modal" class="modal animate__animated animate__fadeIn">
                <div onclick="contributors_close()" class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p id="contributors_title" class="modal-card-title"></p>
                        <button onclick="contributors_close()" class="delete" aria-label="close"></button>
                    </header>
                <section class="modal-card-body">
                    <span>Project Leader: Pablo Ferreiro Romero <a href="https://twitter.com/pablouser1" target="_blank">@pablouser1</a></span>
                </section>
                <footer id="contributors_footer" class="modal-card-foot"></footer>
            </div>
        </div>
    </section>
    <footer id="footer" class="footer">
        <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
            <ul>
                <li><a onclick="changelanguage('en')">English</a></li>
                <li><a onclick="changelanguage('es')">Español</a></li>
            </ul>
        </nav>
    </footer>
    <!-- Yearbook manager -->
    <script src="scripts/yearbook.js"></script>
    <!-- Multilang manager -->
    <script src="scripts/lang.js"></script>
    <!-- Splashscreen handler -->
    <script src="scripts/splashscreen.js"></script>
</body>
</html>
<?php
// Generate HTML file
file_put_contents($baseurl.'index.html', ob_get_contents());
// https://stackoverflow.com/a/19730838 Literally have no clue how this works, but it just works
class HZip 
{ 
  /** 
   * Add files and sub-directories in a folder to zip file. 
   * @param string $folder 
   * @param ZipArchive $zipFile 
   * @param int $exclusiveLength Number of text to be exclusived from the file path. 
   */ 
  private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
    $handle = opendir($folder);
    while (false !== $f = readdir($handle)) {
      if ($f != '.' && $f != '..') {
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip.
        $localPath = substr($filePath, $exclusiveLength);
        if (is_file($filePath)) {
          $zipFile->addFile($filePath, $localPath);
        } elseif (is_dir($filePath)) {
          // Add sub-directory.
          $zipFile->addEmptyDir($localPath);
          self::folderToZip($filePath, $zipFile, $exclusiveLength);
        }
      }
    }
    closedir($handle);
  }
  /**
   * Zip a folder (include itself).
   * Usage:
   *   HZip::zipDir('/path/to/sourceDir', '/path/to/out.zip');
   *
   * @param string $sourcePath Path of directory to be zip.
   * @param string $outZipPath Path of output zip file.
   */
  public static function zipDir($sourcePath, $outZipPath)
  {
    $pathInfo = pathInfo($sourcePath);
    $parentPath = $pathInfo['dirname'];
    $dirName = $pathInfo['basename'];

    $z = new ZipArchive();
    $z->open($outZipPath, ZIPARCHIVE::CREATE);
    self::folderToZip($sourcePath, $z, strlen("$sourcePath"));
    $z->close();
  }
}

// Makes zip from folder
$date_file = date('d-m-Y_his');
$date_db = date("Y-m-d H:i:s");
$zip_name = "yearbook_".$date_file.'.zip';
$zip_path = $baseurl.$zip_name;
HZip::zipDir($baseurl, $zip_path);

// Writes data to DB
$stmt = $conn->prepare("INSERT IGNORE INTO yearbooks(schoolid, schoolyear, zipname, generated, available) VALUES(?, ?, ?, ?, 0)");
$stmt->bind_param("isss", $userinfo["idcentro"], $userinfo["yearuser"], $zip_name, $date_db);
if ($stmt->execute() !== true) {
    die("Error writing data: " . $conn->error);
}
header("Location: dashboard.php");
?>
