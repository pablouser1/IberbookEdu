<?php
session_start();
if (!isset($_SESSION["loggedin"])){
    header("Location: login.php");
}

?>

<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>¡Gracias! - IberbookEdu</title>
        <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">

    <body>
        <section class="hero is-success is-fullheight">
            <div class="hero-body">
              <div class="container">
                <h1 class="title">
                  Gracias por tu colaboración
                </h1>
                <h2 class="subtitle">
                  Tus datos se han procesado correctamente.
                </h2>
                <a class="button is-info" href="dashboard.php">
                  <span class="icon">
                    <i class="fas fa-backward"></i>
                  </span>
                  <span>Volver al panel de control</span>
                </a>
              </div>
            </div>
          </section>
    </body>
</html>