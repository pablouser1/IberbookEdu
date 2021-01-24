<?php
session_start();
if (!isset($_SESSION["owner"])){
    header("Location: ../login.php");
    exit;
}

require_once("../../classes/staff.php");
$staffClass = new Staff;
$staff = $staffClass->getStaff();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff administration - IberbookEdu</title>
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
</head>

<body>
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    Staff administration
                </h1>
                <h2 class="subtitle">
                    Add/remove staff
                </h2>
            </div>
        </div>
    </section>
    <section id="option" class="section tab">
        <div class="container has-text-centered">
            <p class="title">Choose what account you want to administrate</p>
            <div class="buttons is-centered">
                <a href="#admins" class="button is-link">
                    <span class="icon">
                        <i class="fas fa-user-friends"></i>
                    </span>
                    <span>Administrators</span>
                </a>
                <a href="#owners" class="button is-link">
                    <span class="icon">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <span>Owners</span>
                </a>
            </div>
        </div>
    </section>
    <section id="admins" class="section is-hidden tab">
        <div class="control has-text-centered">
            <button id="newadmin" type="button" class="button is-info">
                <span class="icon">
                    <i class="fas fa-user-friends"></i>
                </span>
                <span>Add new admin</span>
            </button>
        </div>
        <form action="mngStaff.php" method="POST">
            <div id="admin_columns" class="columns is-mobile is-multiline">
                <div class="column is-narrow">
                    <div class="card">
                        <header class="card-header">
                            <p class="card-header-title">Admin 0</p>
                        </header>
                        <div class="card-content">
                            <div class="field">
                                <label class="label">Admin</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="username[]" placeholder="user" required>
                                    <span class="icon is-left"><i class="fas fa-user"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <label class="radio">
                        <input value="add" type="radio" name="action">
                        Add
                    </label>
                    <label class="radio">
                        <input value="remove" type="radio" name="action">
                        Remove
                    </label>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" name="sendstaff" value="admin" class="button is-primary">
                        <span class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </span>
                        <span>Send</span>
                    </button>
                </div>
            </div>
        </form>
    </section>
    <section id="owners" class="section is-hidden tab">
        <div class="control has-text-centered">
            <button id="newowner" type="button" class="button is-info">
                <span class="icon">
                    <i class="fas fa-user-friends"></i>
                </span>
                <span>Add another owner</span>
            </button>
        </div>
        <form action="managestaff.php" method="POST">
            <div id="owner_columns" class="columns is-mobile is-multiline">
                <div class="column is-narrow">
                    <div class="card">
                        <div class="card-content">
                            <p class="title has-text-centered">Owner 0</p>
                            <div class="field">
                                <label class="label">User</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="username[]" placeholder="usuario" required>
                                    <span class="icon is-left"><i class="fas fa-user"></i></span>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Password</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="password" name="password[]" placeholder="***********" required>
                                    <span class="icon is-left"><i class="fas fa-key"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <label class="radio">
                        <input value="add" type="radio" name="action">
                        Add
                    </label>
                    <label class="radio">
                        <input value="remove" type="radio" name="action">
                        Remove
                    </label>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" name="sendstaff" value="owner" class="button is-primary">
                        <span class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </span>
                        <span>Send</span>
                    </button>
                </div>
            </div>
        </form>
    </section>
    <hr>
    <section class="section pt-0">
        <p class="title">
            <span class="icon">
                <i class="fas fa-info-circle"></i>
            </span>
            <span>Information</span>
        </p>
        <table class="table is-bordered is-striped is-hoverable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Permissions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($staff as $user){
                    echo "
                    <tr>
                        <td>$user[id]</td>
                        <td>$user[username]</td>
                        <td>$user[permissions]</td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </section>
    <footer class="footer">
        <nav class="breadcrumb is-centered" aria-label="breadcrumbs">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon is-small">
                            <i class="fas fa-undo"></i>
                        </span>
                        <span>Back to main menu</span>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
                        <span class="icon is-small">
                            <i class="fas fa-columns" aria_hidden="true"></i>
                        </span>
                        <span>Back to control panel</span>
                    </a>
                </li>
                <li>
                    <a href="../logout.php">
                        <span class="icon is-small">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                        </span>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </footer>
    <script src="../../assets/scripts/owner/staff.js"></script>
</body>

</html>
