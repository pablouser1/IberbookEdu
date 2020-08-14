<?php
// Generate HTML final document and generate ZIP file //
session_start();
require_once("../helpers/db.php");
ob_start();

// Get vars from before and set a new one
$students = $_SESSION["students"];
$teachers = $_SESSION["teachers"];
$gallery = $_SESSION["gallery"];
$userinfo = $_SESSION["userinfo"];
$baseurl = $_SESSION["baseurl"];
// Copying files
function recursivecopy($source, $dest){
    foreach (
        $iterator = new \RecursiveIteratorIterator(
         new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
         \RecursiveIteratorIterator::SELF_FIRST) as $item
       ) {
         if ($item->isDir()) {
             mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
         } else {
           copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
         }
       }
}
$source = "../assets/styles/yearbook/";
$dest = $baseurl.'styles/';
recursivecopy($source, $dest);
$source = "../assets/scripts/yearbook/";
$dest = $baseurl.'scripts/';
recursivecopy($source, $dest);
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
    <link rel="stylesheet" href="styles/vendor/zuck.min.css">
    <link rel="stylesheet" href="styles/vendor/snapgram.min.css">
    <link rel="stylesheet" href="styles/yearbook.css">
    <!-- User data in JSON, output of PHP -->
    <script type="text/javascript">
        const students_js = <?php echo json_encode($students);?>;
        const teachers_js = <?php echo json_encode($teachers);?>;
        const gallery_js = <?php echo json_encode($gallery);?>;
        const schoolyear_js = '<?php echo($userinfo["yearuser"]);?>';
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
    <!-- Splashscreen -->
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
    <!-- Navigation tabs -->
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
    <!-- Yearbook, includes stories and pics -->
    <section id="yearbook" class="section is-hidden py-0">
        <h1 id="teachers_title" class="title has-text-centered"></h1>
        <div id="stories_teachers"></div>
        <div class="columns is-mobile is-centered is-multiline is-vcentered">
        <?php
        foreach($teachers as $teacher){
            echo '
            <div class="column is-half-mobile is-one-third-tablet is-one-fifth-desktop">
                <div class="card">
                    <div class="card-image">
                        <figure class="image">
                            <img src="'.$teacher["photo"].'" alt="Foto '.$teacher["name"].'">
                            <figcaption style="top:auto;" class="has-text-centered is-overlay">
                                <span class="tag">'.$teacher["subject"].'</span>
                            </figcaption>
                        </figure>
                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">
                                <p class="title is-4">'.$teacher["fullname"]["name"].'</p>
                                <p class="subtitle is-6">'.$teacher["fullname"]["surname"].'</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ';
        }
        ?>
        </div>
        <hr>
        <h1 id="students_title" class="title has-text-centered"></h1>
        <div id="stories_students"></div>
        <div class="columns is-mobile is-centered is-multiline is-vcentered">
        <?php
        foreach($students as $student){
            echo '
            <div class="column is-half-mobile is-one-third-tablet is-one-fifth-desktop">
                <div class="card">
                    <div class="card-image">
                        <figure class="image">
                            <img src="'.$student["photo"].'" alt="Foto '.$student["name"].'">
                        </figure>
                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">
                                <p class="title is-4">'.$student["fullname"]["name"].'</p>
                                <p class="subtitle is-6">'.$student["fullname"]["surname"].'</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            ';
        }
        ?>
        </div>
    </section>
    <!-- Gallery -->
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
        <!-- Modal used for zooming gallery photos -->
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
                <footer class="modal-card-foot"></footer>
            </div>
        </div>
    </section>
    <!-- About section -->
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
    <!-- Footer, currently only used for changing languages -->
    <footer id="footer" class="footer">
        <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
            <ul>
                <li><a onclick="changelanguage('en')">English</a></li>
                <li><a onclick="changelanguage('es')">Español</a></li>
            </ul>
        </nav>
    </footer>
    <!-- Stories library -->
    <script src="scripts/vendor/zuck.min.js"></script>
    <!-- Multilang manager -->
    <script src="scripts/lang.js"></script>
    <!-- Yearbook manager -->
    <script src="scripts/yearbook.js"></script>
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
$dt = new DateTime("now", new DateTimeZone('Europe/Madrid'));
$date_file = $dt->format('d-m-Y_his');
$zip_name = "yearbook_".$date_file.'.zip';
$zip_path = $baseurl.$zip_name;
HZip::zipDir($baseurl, $zip_path);

// Writes data to DB
$stmt = $conn->prepare("INSERT IGNORE INTO yearbooks(schoolid, schoolyear, zipname, available) VALUES(?, ?, ?, 0)");
$stmt->bind_param("iss", $userinfo["idcentro"], $userinfo["yearuser"], $zip_name);
if ($stmt->execute() !== true) {
    die("Error writing data: " . $conn->error);
}
header("Location: dashboard.php");
?>
