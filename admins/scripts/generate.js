// Tabs
function tabchange(seccion){
    var active = document.getElementById("tabs").getElementsByClassName("is-active")[0] // Active tab
    switch(seccion){
        // Tab clicked and section that wants to remove the is-hidden class
        case "yearbook":
            var new_active = document.getElementById("tab_yearbook");
            var new_tab = document.getElementById("yearbook");
            break;
        case "gallery":
            var new_active = document.getElementById("tab_gallery");
            var new_tab = document.getElementById("gallery");
            break;
        case "about":
            var new_active = document.getElementById("tab_about");
            var new_tab = document.getElementById("about");
            break;
        default:
            alert("What")
    }
    switch(active.id){
        // Active section
        case "tab_yearbook":
            var old_tab = document.getElementById("yearbook");
            break;
        case "tab_gallery":
            var old_tab = document.getElementById("gallery");
            break;
        case "tab_about":
            var old_tab = document.getElementById("about");
            break;
        default:
            alert("What")
    }

    if (active !== new_active){
        active.classList.remove("is-active")
        new_active.classList.add("is-active")
        old_tab.classList.add("is-hidden")
        new_tab.classList.remove("is-hidden")
    }
}

// -- Yearbook -- //
var video = {
    section: document.getElementById("video_preview"),
    title: document.getElementById("video_preview_title"),
    subtitle: document.getElementById("video_preview_subtitle"),
    bgvid: document.getElementById("bgvid"),
    link: document.getElementById("video_link"),
    exit: document.getElementById("video_exit"),
    source: null
}
function viewvideo(id, type){
    if (video["source"] === null){
        append_video = 1;
        video["source"] = document.createElement('source');
    }
    switch (type) {
        case "students":
            video["source"].setAttribute('src', students_js[id]["video"])
            video["title"].innerHTML = students_js[id]["name"]
            video["subtitle"].innerHTML = students_js[id]["surnames"]
            break;
        case "teachers":
            video["source"].setAttribute('src', teachers_js[id]["video"])
            video["title"].innerHTML = teachers_js[id]["name"]
            video["subtitle"].innerHTML = teachers_js[id]["surnames"]
            break;
        default:
            break;
    }
    if (append_video === 1){
        video["bgvid"].appendChild(video["source"]);
        append_video = 0;
    }
    // Hide everything
    document.getElementById("banner").classList.add("is-hidden");
    document.getElementById("tabs").classList.add("is-hidden");
    document.getElementById("yearbook").classList.add("is-hidden");
    document.getElementById("footer").classList.add("is-hidden");

    video["section"].classList.remove("is-hidden");
    video["bgvid"].load();
    video["bgvid"].play();
}

video["link"].addEventListener("click", function (){
    if (video["bgvid"].requestFullscreen) {
        video["bgvid"].requestFullscreen();
    } else if (video["bgvid"].mozRequestFullScreen) { /* Firefox */
        video["bgvid"].mozRequestFullScreen();
    } else if (video["bgvid"].webkitRequestFullscreen) { /* Chrome, Safari and Opera */
        video["bgvid"].webkitRequestFullscreen();
    } else if (video["bgvid"].msRequestFullscreen) { /* IE/Edge */
        video["bgvid"].msRequestFullscreen();
    }
})

video["exit"].addEventListener("click", function(){
    video["section"].classList.add("is-hidden");
    // Show everything
    document.getElementById("banner").classList.remove("is-hidden");
    document.getElementById("tabs").classList.remove("is-hidden");
    document.getElementById("yearbook").classList.remove("is-hidden");
    document.getElementById("footer").classList.remove("is-hidden");
    // Stop video
    video["bgvid"].pause()
})

video["bgvid"].addEventListener('fullscreenchange',function() {
    if (document.fullscreenElement && document.fullscreenElement.nodeName == 'VIDEO'){
        document.getElementsByClassName("hero-video")[0].style["pointer-events"] = "auto"
        video["bgvid"].currentTime = 0;
        video["bgvid"].muted = false;
        video["bdvid"].controls = true;
    }
    else{
        document.getElementsByClassName("hero-video")[0].style["pointer-events"] = "none"
        video["bgvid"].muted = true;
        video["bdvid"].controls = false;
    }
})

// -- Gallery -- //
var gallery_modal = document.getElementById("gallery_modal");
var imagen_modal = document.getElementById("imagen_modal");
var gallery_description = document.getElementById("gallery_description");
var source_photo = null;

function viewphoto(id){
    imagen_modal.src = gallery_js[id]["path"]
    // Maybe not working, not tested
    if (gallery_js[id]["description"] !== undefined){
        gallery_description.innerHTML = gallery_js[id]["description"]
    }
    gallery_modal.classList.add("is-active")
}

function exitphoto(){
    gallery_modal.classList.remove("is-active");
}

// About
var contributors_modal = document.getElementById("contributors_modal")
function contributors_open(){
    contributors_modal.classList.add("is-active")
}

function contributors_close(){
    contributors_modal.classList.remove("is-active");
}