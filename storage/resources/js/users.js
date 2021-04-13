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
hashchange()

// CONSTANTS //
var groups_html = ""
for (let i = 0; i < groups.length; i++) {
    const element = groups[i];
    groups_html += `<option value="${element.id}">${element.name} - ${element.school.name}</option>`
}

/*
|--------------------------------------------------------------------------
| ADD USERS
|--------------------------------------------------------------------------
|
| Cards and handler
|
*/
var user_i = 0;
function addUserCard() {
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
                    <label class="label">Group</label>
                    <div class="control">
                        <div class="select is-multiple">
                            <select name="users[${user_i}][groups][]" requiered multiple size="${groups.length}">${groups_html}</select>
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
    document.getElementById("add_columns").insertAdjacentHTML("beforeend", user_html)
    user_i++;
}

addUserCard()

// -- JSON file handler -- //
const jsoninput = document.getElementById("jsoninput")
const fileName = document.getElementById("jsonname")
jsoninput.onchange = () => {
    if (jsoninput.files.length > 0) {
        fileName.textContent = jsoninput.files[0].name;
    }
}

// FORM UPLOAD
document.getElementById("add_form").addEventListener("submit", async (event) => {
    event.preventDefault();
    const formData = new FormData(this);
    try {
        const res = await fetch("../../users", {
            method: 'POST',
            body : formData
        })
        console.log(res)
        const res_json = await res.json()
        console.log(res_json)
        generateTXT(res_json)
    }
    catch (badRes) {
        console.log(badRes)
        alert("Error while generating users")
    }
})

document.getElementById("jsonsend").addEventListener("click", async (event) => {
    event.preventDefault();
    const formData = new FormData;
    formData.append("json", jsoninput.files[0])
    try {
        const res = await fetch("../../users", {
            method: 'POST',
            body : formData
        })
        console.log(res)
        const res_json = await res.json()
        console.log(res_json)
        generateTXT(res_json)
    }
    catch (badRes) {
        console.log(badRes)
        alert("Error while generating users")
    }
})

// Download txt with changes
function generateTXT(users_json) {
    let passwords = ""
    for (let i=0; i<users_json.length; i++) {
        let user = users_json[i]
        passwords += `${user.username}:${user.password} Name:(${user.fullname})\n`
    }
    const blobText = new Blob([passwords], {type: "text/plain"})
    const blobLink = URL.createObjectURL(blobText)
    window.open(blobLink)
}
