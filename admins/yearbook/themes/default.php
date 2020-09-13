<?php
// -- Generate HTML final document and generate ZIP file -- //
session_start();

if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== "admin") {
    header("Location: ../login.php");
}

require_once("../../../helpers/db/db.php");

// Get vars from before
$students = $_SESSION["students"];
$teachers = $_SESSION["teachers"];
$gallery = $_SESSION["gallery"];
$schoolurl = (!empty($_SESSION["schoolurl"])) ? $_SESSION["schoolurl"] : "#";
$userinfo = $_SESSION["userinfo"];
$baseurl = $_SESSION["baseurl"];
$acyear = $_SESSION["acyear"];

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

    <!-- Dev Vue -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script> -->
    <script src="scripts/vendor/vue.js"></script>
    <script defer src="scripts/vendor/solid.min.js"></script>
    <script defer src="scripts/vendor/fontawesome.min.js"></script>
    <script src="scripts/vendor/confetti.min.js"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="styles/vendor/bulma.min.css"/>
    <link rel="stylesheet" href="styles/vendor/animate.min.css"/>
    <link rel="stylesheet" href="styles/vendor/zuck.min.css"/>
    <link rel="stylesheet" href="styles/vendor/snapgram.min.css"/>
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
    <div id="main">
        <!-- Splashscreen -->
        <section v-if="!ready" class="hero is-fullheight">
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
        <section v-if="ready" class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <p class="title has-text-centered">
                        <i class="fas fa-graduation-cap"></i>
                        <span>{{ lang.banner.title }} <?php echo($userinfo["yearuser"]);?></span>
                    </p>
                    <h2 class="subtitle has-text-centered"><?php echo($acyear);?></h2>
                    <h2 v-if="longtimeago" class="subtitle has-text-centered">{{ lang.misc.longtime }}</h2>
                </div>
            </div>
        </section>
        <!-- Navigation tabs -->
        <nav id="navbar" v-if="ready" class="navbar is-primary is-bold is-fixed-top" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a href="<?php echo($schoolurl); ?>" target="_blank" class="navbar-item">
                    <span class="icon">
                        <i class="fas fa-graduation-cap"></i>
                    </span>
                    <span><?php echo($userinfo["namecentro"]);?></span>
                </a>
                <a class="navbar-burger" :class="{ 'is-active': showNav }" @click="showNav = !showNav" role="button" aria-label="menu" aria-expanded="false">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>
            <div class="navbar-menu" :class="{ 'is-active': showNav }">
                <div class="navbar-start">
                    <a href="#yearbook" class="navbar-item">
                        <span class="icon">
                            <i class="fas fa-book-open"></i>
                        </span>
                        <span>Yearbook</span>
                    </a>
                    <a href="#gallery" class="navbar-item">
                        <span class="icon">
                            <i class="fas fa-photo-video"></i>
                        </span>
                        <span>{{ lang.tabs.gallery }}</span>
                    </a>
                    <a href="#about" class="navbar-item">
                        <span class="icon">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <span>{{ lang.tabs.about }}</span>
                    </a>
                </div>
            </div>
        </nav>
        <!-- Yearbook and Gallery (used by Vue.js) includes stories -->
        <section id="yearbook" v-show="ready" class="section tab">
            <!-- Teachers -->
            <h1 class="title has-text-centered">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>{{ lang.yearbook.teachers }}</span>
            </h1>
            <p class="subtitle has-text-centered">Total: <?php echo(count($teachers));?></p>
            <div class="container box">
                <div id="stories_teachers"></div>
            </div>
            <br>
            <teachers v-bind:teachers="teachers"></teachers>
            <hr>
            <!-- Students -->
            <h1 class="title has-text-centered">
                <i class="fas fa-user-graduate"></i>
                <span>{{ lang.yearbook.students }}</span>
            </h1>
            <p class="subtitle has-text-centered">Total: <?php echo(count($students));?></p>
            <div class="container box">
                <div id="stories_students"></div>
            </div>
            <br>
            <students v-bind:students="students"></students>
        </section>
        <!-- Gallery -->
        <section id="gallery" class="section is-hidden tab">
            <gallery v-bind:gallery="gallery"></gallery>
        </section>
        <!-- About section -->
        <section id="about" class="section is-hidden tab">
            <div class="container">
                <p v-html="lang.about.attribution"></p>
                <hr>
                <p v-html="lang.about.credits"></p>
                <br>
                <span>Project Leader: Pablo Ferreiro Romero <a href="https://twitter.com/pablouser1" target="_blank">@pablouser1</a></span>
            </div>
        </section>
        <!-- Document footer -->
        <footer v-if="ready" id="footer" class="footer">
            <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
                <ul id="languages">
                    <li><a v-on:click="changelang('en')">English</a></li>
                    <li><a v-on:click="changelang('es')">Español</a></li>
                </ul>
            </nav>
            <p v-html="lang.footer.madewith" class="has-text-centered"></p>
        </footer>
    </div>
    <!-- Stories library -->
    <script src="scripts/vendor/zuck.min.js"></script>
    <!-- Multilang manager -->
    <script src="scripts/lang.js"></script>
    <!-- Yearbook manager -->
    <script src="scripts/yearbook.js"></script>
    <!-- Stories manager -->
    <script src="scripts/stories.js"></script>
    <!-- Misc -->
    <script src="scripts/misc.js"></script>
</body>
</html>
<?php
// Generate HTML file
file_put_contents($baseurl.'/index.html', ob_get_contents());
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
HZip::zipDir($baseurl, $baseurl."/".$zip_name);

// Writes data to DB
$stmt = $conn->prepare("INSERT INTO yearbooks(schoolid, schoolname, schoolyear, zipname, acyear) VALUES(?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $userinfo["idcentro"], $userinfo["namecentro"], $userinfo["yearuser"], $zip_name, $acyear);
if ($stmt->execute() !== true) {
    die("Error writing data: " . $conn->error);
}

header("Location: ../../dashboard.php");
?>
