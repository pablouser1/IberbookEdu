var genyearbook = document.getElementById("genyearbook")
var theme = document.getElementById("theme_selector")

theme.addEventListener("change", function () {
    // Get theme
    let gentheme = theme.options[theme.selectedIndex].text;
    // Get current url
    let url = new URL(genyearbook.href)
    // Get params
    let search_params = url.searchParams;
    // Set new param from select
    search_params.set('theme', gentheme);
    // Insert to anchor
    url.theme = search_params.toString()
    genyearbook.href = url.toString();
})

genyearbook.addEventListener("click", () => {
    document.getElementById("progress").classList.remove("is-hidden")
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
