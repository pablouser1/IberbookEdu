<?php
session_start();
if (!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] !== "admin"){
    header("HTTP/1.1 403 Forbidden");
    die("No tienes los permisos necesarios para acceder a esta página");
}

if(isset($_GET["user"])){
  $_SESSION["loggedin"] = "user";
  header("Location: ../users/dashboard.php");
}
elseif(isset($_GET["admin"])){
  $_SESSION["loggedin"] = "admin";
  header("Location: ../admins/dashboard.php");
}

$userinfo = $_SESSION["userinfo"];
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Selección de cuenta - IberbookEdu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.0/css/bulma.min.css">
</head>

<body>
  <section class="hero is-primary">
    <div class="hero-body">
      <div class="container">
        <h1 class="title">
          Bienvenido, <?php echo($userinfo["nameuser"]);?>
        </h1>
        <h2 class="subtitle">
          Elige un perfil
        </h2>
      </div>
    </div>
  </section>
  <section class="section">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="get">
      <div class="columns is-mobile is-multiline is-centered">
        <div class="column is-narrow">
          <div class="card">
            <div class="card-content">
              <p class="title">Usuario</p>
              <button name="user" type="submit" class="button">Iniciar sesión</button>
            </div>
          </div>
        </div>
        <div class="column is-narrow">
          <div class="card">
            <div class="card-content">
              <p class="title">Administrador</p>
              <button name="admin" type="submit" class="button">Iniciar sesión</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </section>
</body>

</html>