function closedelete() {
    document.getElementById("delete_modal").classList.remove("is-active")
}

try{
    document.getElementById("delete_button").addEventListener("click", function(){
        document.getElementById("delete_modal").classList.add("is-active")
    })
    document.getElementById("delete_cancel").addEventListener("click", closedelete)
}
catch(error){
    console.log("No existente")
}
