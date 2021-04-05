<?php
require_once("config/config.php");
require_once("lang/lang.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo L::index_instance ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
  </head>
  <body>
    <section class="hero is-fullheight">
      <div class="hero-body">
        <div class="container">
          <h1 class="title">
          <?php echo( L::index_title . " " . $server["name"] ); ?>
          </h1>
          <h2 class="subtitle">
            <?php echo($server["description"]); ?>
          </h2>
          <p><?php echo L::index_explanation; ?></p>
          <a class="button is-info" href="https://github.com/pablouser1/Iberbookedu-backend"><?php echo L::index_moreinfo ?></a>
        </div>
      </div>
    </section>
  </body>
</html>
