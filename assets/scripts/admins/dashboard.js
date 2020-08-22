document.getElementById("genyearbook").addEventListener("click", () => {
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
