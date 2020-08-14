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
let teachers_stories = new Zuck('stories_teachers', {
    skin: 'snapgram', // Skin used
    avatars: true, // shows user photo instead of last story item preview
    list: false, // displays a timeline instead of carousel
    openEffect: true, // enables effect when opening story
    cubeEffect: true, // enables the 3d cube effect when sliding story
    autoFullScreen: true, // enables fullscreen on mobile browsers
    backButton: true, // adds a back button to close the story viewer
    backNative: false, // uses window history to enable back button on browsers/android
    previousTap: true, // use 1/3 of the screen to navigate to previous item when tap the story
    localStorage: true, // set true to save "seen" position. Element must have a id to save properly.
    reactive: false, // set true if you use frameworks like React to control the timeline (see react.sample.html)

    stories: teachers_js,
    language: lang["stories"]
});

let students_stories = new Zuck('stories_students', {
    skin: 'snapgram', // Skin used
    avatars: true, // shows user photo instead of last story item preview
    list: false, // displays a timeline instead of carousel
    openEffect: true, // enables effect when opening story
    cubeEffect: true, // enables the 3d cube effect when sliding story
    autoFullScreen: true, // enables fullscreen on mobile browsers
    backButton: true, // adds a back button to close the story viewer
    backNative: false, // uses window history to enable back button on browsers/android
    previousTap: true, // use 1/3 of the screen to navigate to previous item when tap the story
    localStorage: true, // set true to save "seen" position. Element must have a id to save properly.
    reactive: false, // set true if you use frameworks like React to control the timeline (see react.sample.html)

    stories: students_js,
    language: lang["stories"]
});
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