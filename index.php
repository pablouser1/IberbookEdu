<?php
require_once("config/config.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instancia IberbookEdu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
  </head>
  <body>
    <section class="hero is-fullheight">
      <div class="hero-body">
        <div class="container">
          <h1 class="title">
            ¡Bienvenido! Instancia IberbookEdu: <?php echo($server["name"]); ?>
          </h1>
          <h2 class="subtitle">
            <?php echo($server["description"]); ?>
          </h2>
          <p>Esta es una instancia de IberbookEdu, haz click <a href="https://iberbookedu.onrender.com">aquí</a> para continuar</p>
        </div>
      </div>
    </section>
  </body>
</html>
