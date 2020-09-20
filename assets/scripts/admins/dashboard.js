var genyearbook = document.getElementById("genyearbook")
var theme_select = document.getElementById("theme_selector")
var theme = "default" // Set default theme

theme_select.addEventListener("change", function () {
    // Get theme
    theme = theme_select.options[theme_select.selectedIndex].text;
})

genyearbook.addEventListener("click", function () {
    // Set loading
    genyearbook.classList.add("is-loading")
    document.body.style.cursor = "progress"; 
    // Send id and action to do
    fetch(`yearbook/generate.php?theme=${theme}`)

    // Get json response
    .then(res => {
        return res.json()
    })
    .then(json_res => {
        alert(json_res["description"])
        document.body.style.cursor = "pointer"; 
        if (json_res["code"] == "C") {
            // If everyting went ok, reload page
            location.reload();
        }
    })
})

document.getElementById("delete_user").addEventListener("click", () => {
    var userid = document.getElementById("select_user").value;
    // userid = int => students
    // userid = string => teachers
    if (isNaN(Number(userid))) {
        var type = "teachers"
    }
    else {
        var type = "students"
    }
    window.location.href = `manageusers.php?id=${userid}&type=${type}`;
})
