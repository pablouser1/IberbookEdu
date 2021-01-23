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

// -- Add users -- //
const users_columns = document.getElementById("add_columns")

// Options //
// Schools html
var schools_html = ""
for (var i=0; i<schools.length; i++) {
    schools_html += `<option value="${schools[i].id}">${schools[i].name}</option>`
}

// Groups html
var groups_html = ""
for (var i=0; i<groups.length; i++) {
    groups_html += `<option>${groups[i].name}</option>`
}

var user_i = 0;
function appendAddUserCard() {
    const user_html = `
    <div class="column is-narrow">
        <div class="card">
            <header class="card-header">
                <p class="card-header-title">User ${user_i}</p>
            </header>
            <div class="card-content">
                <div class="field">
                    <label class="label">Username</label>
                    <div class="control">
                        <input name="users[${user_i}][username]" class="input" type="text" required autocomplete="off">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Full name</label>
                    <div class="control">
                        <input name="users[${user_i}][fullname]" class="input" type="text" required autocomplete="off">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Group</label>
                    <div class="control">
                        <div class="select">
                            <select name="users[${user_i}][schoolyear]" requiered>${groups_html}</select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label">School</label>
                    <div class="control">
                        <div class="select">
                            <select name="users[${user_i}][schoolid]" requiered>${schools_html}</select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Type</label>
                    <div class="control">
                        <div class="select">
                            <select name="users[${user_i}][type]" requiered>
                                <option value="students">Students</option>
                                <option value="teachers">Teachers</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `
    users_columns.insertAdjacentHTML("beforeend", user_html)
    user_i++;
}

document.getElementById("add_form").addEventListener("submit", function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch("manageusers.php", {
        method: 'POST',
        body : formData
    })
    .then(response => response.json())
    .then(
        success => console.log(success)
    )
    .catch(
        error => console.log(error)
    );
})
// First user append
appendAddUserCard()

// -- CSV file handler -- //
const csvinput = document.getElementById("csvinput")
const fileName = document.getElementById("csvname")
csvinput.onchange = () => {
    if (csvinput.files.length > 0) {
        fileName.textContent = csvinput.files[0].name;
    }
}

document.getElementById("csvsend").addEventListener("click", function () {
    const csv = csvinput.files[0];
    fetch("manageusers.php?action=csv", {
        method: 'POST',
        body: csv
    })
    .then(response => response.json())
    .then(
        success => console.log(success)
    )
    .catch(
        error => console.log(error)
    );
})

hashchange()
