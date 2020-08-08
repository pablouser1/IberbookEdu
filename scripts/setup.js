document.getElementById("preparations").addEventListener("click", function(){
    document.getElementById("welcome").classList.add("is-hidden")
    document.getElementById("database").classList.remove("is-hidden")
})

document.getElementById("database_next").addEventListener("click", function(){
    var forms = document.getElementsByName('db[]');
    var empty = true;
    for(key=0; key < forms.length; key++)  {
        if(forms[key].value == ""){
            empty = false;
        }
    }
    // Continue if all fields are filled
    if(empty == true){
        document.getElementById("database").classList.add("is-hidden")
        document.getElementById("owner").classList.remove("is-hidden")
    }
    else{
        alert("Hay entradas en blanco")
    }
})

document.getElementById("database_back").addEventListener("click", function (){
    document.getElementById("owner").classList.add("is-hidden")
    document.getElementById("database").classList.remove("is-hidden")
})

document.getElementById("sendall").addEventListener("click", function (){
    document.getElementById("progress").classList.remove("is-hidden")
})