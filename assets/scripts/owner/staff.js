// -- Main menu -- //
function hashchange() {
    var hash = window.location.hash.substring(1);
    if (hash == "") {
        hash = "option"
    }
    var old_tab = document.querySelector(".tab:not(.is-hidden)")
    var new_tab = document.getElementById(hash);
    old_tab.classList.add("is-hidden")
    new_tab.classList.remove("is-hidden")
}
// -- Common -- //
window.addEventListener("hashchange", hashchange)

function addcolumn(columns, html) {
    columns.insertAdjacentHTML('beforeend', html)
}
// -- Manage admins -- //
var admin_columns = document.getElementById("admin_columns")
var admin_i = 1;
document.getElementById("newadmin").addEventListener("click", () => {
    var html = `
    <div class="column is-narrow">
        <div class="card">
            <div class="card-content">
                <p class="title has-text-centered">Admin ${admin_i}</p>
                <div class="field">
                    <label class="label">Usuario</label>
                    <div class="control has-icons-left">
                        <input class="input" type="text" name="username[]" placeholder="usuario" required>
                        <span class="icon is-left"><i class="fas fa-user"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `
    addcolumn(admin_columns, html)
    admin_i++;
})
// -- Manage owners -- //
var owner_columns = document.getElementById("owner_columns")
var owner_i = 1
document.getElementById("newowner").addEventListener("click", () => {
    var html = `
    <div class="column is-narrow">
        <div class="card">
            <div class="card-content">
                <p class="title has-text-centered">Dueño ${owner_i}</p>
                <div class="field">
                    <label class="label">Usuario</label>
                    <div class="control has-icons-left">
                        <input class="input" type="text" name="username[]" placeholder="usuario" required>
                        <span class="icon is-left"><i class="fas fa-user"></i></span>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Contraseña</label>
                    <div class="control has-icons-left">
                        <input class="input" type="text" name="password[]" placeholder="***********" required>
                        <span class="icon is-left"><i class="fas fa-key"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `
    addcolumn(owner_columns, html)
    owner_i++;
})

hashchange()
