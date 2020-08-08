try{
    document.getElementById("delete_button").addEventListener("click", function(){
        document.getElementById("delete_modal").classList.add("is-active")
    })
    document.getElementById("delete_cancel").addEventListener("click", function(){
        document.getElementById("delete_modal").classList.remove("is-active")
    })
}
catch(error){
    console.log("No existente")
}