window.addEventListener("load", function(){
    document.getElementById("loading_body").classList.add("animate__animated", "animate__flipOutX");
    document.getElementById("loading_body").addEventListener("animationend", function(){
        document.getElementById("loading").classList.add("is-hidden")
        document.getElementById("banner").classList.remove("is-hidden")
        document.getElementById("tabs").classList.remove("is-hidden")
        document.getElementById("yearbook").classList.remove("is-hidden")
        confetti.start(1000)
    });
})