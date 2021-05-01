<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yearbook Setup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.2/css/bulma.min.css">
</head>

<body>
    <section class="hero is-primary is-medium">
        <div class="hero-body">
            <p class="title has-text-centered">IberbookEdu Setup</p>
        </div>
    </section>
    <section class="section">
        <form method="post">
            <div class="container">
                <h1 class="title">Server details</h1>
                <h2 class="title">Owner's account</h2>
                <h2 class="subtitle">This account will have full permissions on the instance</h2>
                <div class="field">
                    <label class="label">Username</label>
                    <div class="control">
                        <input name="owner[username]" class="input" type="text" placeholder="Ej: user" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Password</label>
                    <div class="control">
                        <input name="owner[password]" class="input" type="password" placeholder="***********" required>
                    </div>
                </div>
            </div>
            <hr />
            <div class="container">
                <div class="field">
                    <div class="control">
                        <button type="submit" class="button is-success">
                            <span>Send all</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</body>

</html>
