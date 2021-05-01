<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Owner Dashboard - IberbookEdu</title>
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.2/css/bulma.min.css">
</head>

<body>
    <div id="main">
        <section class="hero is-info welcome is-small">
            <div class="hero-body">
                <div class="container">
                    <h1 class="title has-text-centered">
                        Welcome: {$user->username}
                    </h1>
                </div>
            </div>
        </section>
        <section id="items" class="section">
            <div>
                <div>
                    <p class="title is-4">
                        <i class="fas fa-user-shield"></i>
                        <span>Staff</span>
                    </p>
                    <table class="table is-bordered is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            {loop="$staff" as $staff_user}
                                <tr>
                                    <td>{$staff_user->id}</td>
                                    <td>{$staff_user->username}</td>
                                    <td>{$staff_user->role}</td>
                                </tr>
                            {/loop}
                        </tbody>
                    </table>
                    <button id="staff_button" class="button is-info">
                        <span class="icon">
                            <i class="fas fa-user-friends"></i>
                        </span>
                        <span>Add staff</span>
                    </a>
                </div>
                <hr>
                <div>
                    <p class="title is-4">
                        <i class="fas fa-school"></i>
                        <span>Schools</span>
                    </p>
                    <table class="table is-bordered is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            {loop="$schools" as $school}
                                <tr>
                                    <td>{$school->id}</td>
                                    <td>{$school->name}</td>
                                </tr>
                            {/loop}
                        </tbody>
                    </table>
                    <button id="schools_button" type="button" class="button is-info">
                        <span class="icon">
                            <i class="fas fa-school"></i>
                        </span>
                        <span>Add schools</span>
                    </button>
                </div>
                <hr>
                <div>
                    <p class="title is-4">
                        <i class="fas fa-school"></i>
                        <span>Groups</span>
                    </p>
                    <table class="table is-bordered is-striped is-hoverable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>School id</th>
                            </tr>
                        </thead>
                        <tbody>
                            {loop="$groups" as $group}
                                <tr>
                                    <td>{$group->id}</td>
                                    <td>{$group->name}</td>
                                    <td>{$group->school_id}</td>
                                </tr>
                            {/loop}
                        </tbody>
                    </table>
                    <button id="groups_button" type="button" class="button is-info">
                        <span class="icon">
                            <i class="fas fa-users"></i>
                        </span>
                        <span>Add groups</span>
                    </button>
                </div>
                <hr>
                <div>
                    <p class="title is-4">
                        <i class="fas fa-users"></i>
                        <span>Users/Profiles</span>
                    </p>
                    <a href="users" class="button is-info">Add/remove users or profiles</a>
                </div>
            </div>
        </section>
    </div>
    <!-- Modals -->

    <!-- Staff -->
    <div class="modal" id="staff_modal">
        <form action="../../staff" method="POST">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Staff</p>
                    <button class="delete" aria-label="close" data-bulma-modal="close"></button>
                </header>
                <section class="modal-card-body">
                    <div class="field">
                        <div class="control">
                            <input name="Staff[username]" class="input" placeholder="Username" />
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <input name="staff[password]" class="input" type="password" placeholder="Password" />
                        </div>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button type="submit" class="button is-success">Send</button>
                    <button class="button" data-bulma-modal="close">Cancel</button>
                </footer>
            </div>
        </form>
    </div>
    <!-- Schools -->
    <div class="modal" id="schools_modal">
        <form action="../../schools" method="POST">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Schools</p>
                    <button class="delete" aria-label="close" data-bulma-modal="close"></button>
                </header>
                <section class="modal-card-body">
                    <div class="field">
                        <div class="control">
                            <input name="school" class="input" placeholder="School name" />
                        </div>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button type="submit" class="button is-success">Send</button>
                    <button class="button" data-bulma-modal="close">Cancel</button>
                </footer>
            </div>
        </form>
    </div>

    <!-- Groups -->
    <div class="modal" id="groups_modal">
        <form action="../../groups" method="POST">
            <div class="modal-background"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Groups</p>
                    <button class="delete" aria-label="close" data-bulma-modal="close"></button>
                </header>
                <section class="modal-card-body">
                    <div class="field">
                        <div class="control">
                            <input name="group[name]" class="input" placeholder="Group" />
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <input name="group[school]" class="input" placeholder="School id" />
                        </div>
                    </div>
                </section>
                <footer class="modal-card-foot">
                    <button type="submit" class="button is-success">Send</button>
                    <button class="button" data-bulma-modal="close">Cancel</button>
                </footer>
            </div>
        </form>
    </div>
    <script src="../../pages/js/modal.js"></script>
    <script src="../../pages/js/dashboard.js"></script>
</body>

</html>

