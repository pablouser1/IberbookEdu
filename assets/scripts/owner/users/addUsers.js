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

// -- Add users -- //
const users_columns = document.getElementById("add_columns")
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
                    <label class="label">Name</label>
                    <div class="control">
                        <input name="users[${user_i}][name]" class="input" type="text" required autocomplete="off">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Surname</label>
                    <div class="control">
                        <input name="users[${user_i}][surname]" class="input" type="text" required autocomplete="off">
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
                    <label class="label">Group</label>
                    <div class="control">
                        <div class="select">
                            <select name="users[${user_i}][schoolyear]" requiered>${groups_html}</select>
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
    fetch("mngUsers.php?action=add", {
        method: 'POST',
        body : formData
    })
    .then(response => response.json())
    .then(function(res_json) {
        if (res_json.code === "C") {
            generateTXT(res_json.data)
        }
        else {
            alert("Error while generating users")
        }
    })
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
    let formData = new FormData;
    formData.append("csv", csv)
    fetch("mngUsers.php?action=csv", {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(function(res_json) {
        if (res_json.code === "C") {
            generateTXT(res_json.data)
        }
        else {
            alert("Error while generating users")
        }
    })
    .catch(
        error => console.log(error)
    );
})

// Download txt with changes
function generateTXT(users_json) {
    let passwords = ""
    for (let i=0; i<users_json.length; i++) {
        let user = users_json[i]
        passwords += `${user.username}:${user.password}\n`
    }
    const blobText = new Blob([passwords], {type: "text/plain"})
    const blobLink = URL.createObjectURL(blobText)
    window.open(blobLink)
}
