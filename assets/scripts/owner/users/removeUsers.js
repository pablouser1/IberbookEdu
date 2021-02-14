const select_users = document.getElementById("remove_select")

for (let i=0; i<users.length; i++) {
    var option = document.createElement("option")
    option.value = users[i].id
    option.innerText = users[i].name
    select_users.appendChild(option)
}

select_users.size = users.length

document.getElementById("remove_form").addEventListener("submit", function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch("mngUsers.php?action=remove", {
        method: 'POST',
        body : formData
    })
    .then(response => response.json())
    .then(function(res_json) {
        if (res_json.code === "C") {
            location.hash = ""
            location.reload()
        }
        else {
            alert("Error while deleting users")
        }
    })
    .catch(
        error => console.log(error)
    );
})
