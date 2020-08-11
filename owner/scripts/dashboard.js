var modalstaff = document.getElementById("modalstaff")
document.getElementById("managestaff").addEventListener("click", function (){
    modalstaff.classList.add("is-active")
})

var type_select = document.getElementById("typestaff")
var password_field = document.getElementById("password")
var addstaff = document.getElementById("addstaff")
var removestaff = document.getElementById("removestaff")

type_select.addEventListener("change", function (){
    var type_value = type_select.value
    switch (type_value) {
        case "admin":
            password_field.classList.add("is-hidden")
            password_field.required = false
            addstaff.value = "admin"
            removestaff.value = "admin"
            break;
        case "owner":
            password_field.classList.remove("is-hidden")
            password_field.required = true
            addstaff.value = "owner"
            removestaff.value = "owner"
            break;
        default:
            break;
    }
})

function closestaff(){
    modalstaff.classList.remove("is-active")
}