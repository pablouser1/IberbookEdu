var modalschool = document.getElementById("modalschool")
var modaldb = document.getElementById("modalarchive")

document.getElementById("manageschool").addEventListener("click", function (){
    modalschool.classList.add("is-active")
})

document.getElementById("archive").addEventListener("click", function (){
    modaldb.classList.add("is-active")
})

function closeadmin(){
    modaladmin.classList.remove("is-active")
}

function closeschool(){
    modalschool.classList.remove("is-active")
}

function closearchive(){
    modaldb.classList.remove("is-active")
}
