var modalschool = document.getElementById("modalschool")
var modalclear = document.getElementById("modalclear")

document.getElementById("manageschool").addEventListener("click", function (){
    modalschool.classList.add("is-active")
})

document.getElementById("clear").addEventListener("click", function (){
    modalclear.classList.add("is-active")
})

function closeschool(){
    modalschool.classList.remove("is-active")
}

function closeclear(){
    modalclear.classList.remove("is-active")
}
