<?php
// -- Generate HTML final document and generate ZIP file -- //
session_start();

if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== "admin") {
    header("Location: ../login.php");
}

require_once("../helpers/db.php");

// Deleting files
function recursivedelete($dir) {
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()) rmdir($file->getRealPath());
        else unlink($file->getRealPath());
    }
    rmdir($dir);
}

// Get vars from before and set a new one
$students = $_SESSION["students"];
$teachers = $_SESSION["teachers"];
$gallery = $_SESSION["gallery"];
$userinfo = $_SESSION["userinfo"];
$zipdir = $_SESSION["zipdir"];
$tempdir = $_SESSION["tempdir"];

// Get date (used later)
$dt = new DateTime("now", new DateTimeZone('Europe/Madrid'));
ob_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yearbook <?php echo($userinfo["namecentro"]);?></title>
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico">
    <!-- Scripts -->
    <script defer src="scripts/vendor/fontawesome.js"></script>
    <script src="scripts/vendor/confetti.min.js"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="styles/vendor/bulma.min.css">
    <link rel="stylesheet" href="styles/vendor/animate.min.css"/>
    <link rel="stylesheet" href="styles/vendor/zuck.min.css">
    <link rel="stylesheet" href="styles/vendor/snapgram.min.css">
    <link rel="stylesheet" href="styles/yearbook.css">
    <!-- User data in JSON, output of PHP -->
    <script type="text/javascript">
        const teachers_js = <?php echo json_encode($teachers);?>;
        const students_js = <?php echo json_encode($students);?>;
        const gallery_js = <?php echo json_encode($gallery);?>;
        const ybdate_js = <?php echo($dt->getTimestamp());?>;
    </script>
</head>

<body class="has-navbar-fixed-top">
    <!-- NoScript Warning -->
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
    <!-- Banner -->
    <section id="banner" class="hero is-primary is-hidden">
        <div class="hero-body">
            <div class="container">
                <h1 class="title has-text-centered"><?php echo($userinfo["yearuser"]);?></h1>
                <h2 class="subtitle has-text-centered"></h2>
                <h2 id="recap" class="subtitle has-text-centered"></h2>
            </div>
        </div>
    </section>
    <!-- Navigation tabs -->
    <nav id="navbar" class="navbar is-hidden is-primary is-bold is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item">
                <i class="fas fa-graduation-cap"></i>
                <span><?php echo($userinfo["namecentro"]);?></span>
            </a>
            <a id="navbar-burger" role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div id="navbarMenu" class="navbar-menu">
            <div class="navbar-start">
                <a href="#yearbook" class="navbar-item">
                    <span class="icon">
                        <i class="fas fa-book-open"></i>
                    </span>
                    <span>Yearbook</span>
                </a>
                <a href="#gallery" class="navbar-item">
                    <span class="icon">
                        <i class="fas fa-images"></i>
                    </span>
                    <span id="a_gallery"></span>
                </a>
                <a href="#about" class="navbar-item">
                    <span class="icon">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <span id="a_about"></span>
                </a>
            </div>
        </div>
    </nav>
    <!-- Yearbook, includes stories and pics -->
    <section id="yearbook" class="section is-hidden tab">
        <h1 class="title has-text-centered">
            <i class="fas fa-chalkboard-teacher"></i>
            <span id="teachers_title"></span>
        </h1>
        <p class="subtitle has-text-centered">Total: <?php echo(count($teachers));?></p>
        <div class="container">
            <div id="stories_teachers"></div>
        </div>
        <br>
        <div class="columns is-mobile is-centered is-multiline is-vcentered">
        <?php
        foreach($teachers as $teacher){
            echo '
            <div class="column is-full-mobile is-one-third-tablet is-one-fifth-desktop">
                <article class="media">
                    <div class="media-content">
                        <p>
                            <strong>'.$teacher["fullname"]["name"]." ".$teacher["fullname"]["surname"].'</strong> <small>@'.str_replace(" ", "", $teacher["name"]).'</small>
                            <a href="'.$teacher["photo"].'" target="_blank">
                                <figure class="image figure_yearbook">
                                    <img src="'.$teacher["photo"].'">
                                    <figcaption style="top:auto;" class="has-text-centered is-overlay">
                                        <span class="tag">'.$teacher["subject"].'</span>
                                    </figcaption>
                                </figure>
                            </a>
                            <span>'.$teacher["quote"].'</span>
                            <br>
                            <i><small>'.$teacher["date"].'</small></i>
                        </p>
                        <nav class="level is-mobile">
                            <div class="level-left">
                                <a class="level-item">
                                    <span class="icon is-small"><i class="fas fa-reply"></i></span>
                                </a>
                                <a class="level-item">
                                    <span class="icon is-small"><i class="fas fa-retweet"></i></span>
                                </a>
                                <a class="level-item">
                                    <span class="icon is-small"><i class="fas fa-heart"></i></span>
                                </a>
                            </div>
                        </nav>
                    </div>
                </article>
            </div>
            ';
        }
        ?>
        </div>
        <hr>
        <h1 class="title has-text-centered">
            <i class="fas fa-user-graduate"></i>
            <span id="students_title"></span>
        </h1>
        <p class="subtitle has-text-centered">Total: <?php echo(count($students));?></p>
        <div class="container">
            <div id="stories_students"></div>
        </div>
        <br>
        <div class="columns is-mobile is-centered is-multiline is-vcentered">
        <?php
        foreach($students as $student){
            echo '
            <div class="column is-full-mobile is-one-third-tablet is-one-fifth-desktop">
                <article class="media">
                    <div class="media-content">
                        <p>
                            <strong>'.$student["fullname"]["name"]." ".$student["fullname"]["surname"].'</strong> <small>@'.str_replace(" ", "", $student["name"]).'</small>
                            <a href="'.$student["photo"].'" target="_blank">
                                <figure class="image figure_yearbook">
                                    <img src="'.$student["photo"].'">
                                </figure>
                            </a>
                            <span>'.$student["quote"].'</span>
                            <br>
                            <i><small>'.$student["date"].'</small></i>
                        </p>
                        <nav class="level is-mobile">
                            <div class="level-left">
                                <a class="level-item">
                                    <span class="icon is-small"><i class="fas fa-reply"></i></span>
                                </a>
                                <a class="level-item">
                                    <span class="icon is-small"><i class="fas fa-retweet"></i></span>
                                </a>
                                <a class="level-item">
                                    <span class="icon is-small"><i class="fas fa-heart"></i></span>
                                </a>
                            </div>
                        </nav>
                    </div>
                </article>
            </div>
            ';
        }
        ?>
        </div>
    </section>
    <!-- Gallery -->
    <section id="gallery" class="section is-hidden tab">
        <div class="columns is-centered is-multiline">
            <?php
            foreach ($gallery as $id => $pic) {
                echo <<<EOL
                <div class="column is-full-mobile is-one-third-tablet is-one-fifth-desktop">
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
    <section id="about" class="section is-hidden tab">
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
            </div>
        </div>
    </section>
    <!-- Footer, currently only used for changing languages -->
    <footer id="footer" class="footer">
        <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
            <ul id="languages">
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
    <!-- Misc -->
    <script src="scripts/misc.js"></script>
    <!-- Splashscreen handler -->
    <script src="scripts/splashscreen.js"></script>
</body>
</html>
<?php
// Generate HTML file
file_put_contents($tempdir.'/index.html', ob_get_contents());
// https://stackoverflow.com/a/19730838 Literally have no clue how this works, but it just works. Generate ZIP
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
        $localPath = "yearbook".substr($filePath, $exclusiveLength);
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
    $dirName = $pathInfo['basename'];

    $z = new ZipArchive();
    $z->open($outZipPath, ZIPARCHIVE::CREATE);
    self::folderToZip($sourcePath, $z, strlen("$sourcePath"));
    $z->close();
  }
}

// Makes zip from folder
$date_file = $dt->format('d-m-Y');
$zip_name = "yearbook_".$date_file.'.zip';
HZip::zipDir($tempdir, $zipdir."/".$zip_name);

// Writes data to DB
$stmt = $conn->prepare("INSERT IGNORE INTO yearbooks(schoolid, schoolyear, zipname, available) VALUES(?, ?, ?, 0)");
$stmt->bind_param("iss", $userinfo["idcentro"], $userinfo["yearuser"], $zip_name);
if ($stmt->execute() !== true) {
    die("Error writing data: " . $conn->error);
}

// Delete temp dir
recursivedelete($tempdir);

header("Location: dashboard.php");
?>
