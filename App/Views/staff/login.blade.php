<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - IberbookEdu</title>
    <script defer src="https://use.fontawesome.com/releases/v5.15.1/js/all.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.2/css/bulma.min.css">
    <link rel="stylesheet" href="../storage/resources/css/login.css"/>
</head>

<body>
    <section class="container">
        <div class="columns is-multiline">
            <div class="column is-8 is-offset-2 login">
                <div class="columns">
                    <div class="column left">
                        <h1 class="title is-1">IberbookEdu</h1>
                        <h2 class="subtitle colored is-4">Login with your credentials</h2>
                    </div>
                    <div class="column right has-text-centered">
                        <!-- FORM -->
                        <form method="POST">
                            <div class="field">
                                <label class="label">Username</label>
                                <div class="control">
                                    <input name="username" class="input is-medium" type="text" placeholder="user">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Password</label>
                                <div class="control">
                                    <input name="password" class="input is-medium" type="password" placeholder="**********">
                                </div>
                            </div>
                            <button class="button is-block is-primary is-fullwidth is-medium">Login</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="column is-8 is-offset-2">
                <nav class="level">
                    <div class="level-left">
                        <div class="level-item">
                            Made with ❤️ in Github
                        </div>
                    </div>
                    <div class="level-right">
                        <a class="level-item" href="https://github.com/pablouser1/IberbookEdu-backend">About</a>
                    </div>
                </nav>
            </div>
        </div>
    </section>
</body>

</html>
