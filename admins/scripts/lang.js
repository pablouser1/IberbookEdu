// -- Translations -- //
// English translations
const lang_en = {
    common: {
        graduation: "Graduation "
    },
    tabs: {
        gallery: "Gallery",
        about: "About"
    },
    yearbook: {
        students: "Students",
        teachers: "Teachers",
        video_view: "View video",
        video_exit: "Exit"
    },
    gallery: {
        modal_title: "Picture"
    },
    about: {
        attribution: `This project wouldn't be possible without the help of the following proyects 
        distributed under <a href="externalprojects_licenses.txt" target="_blank">the following licenses</a>:<br>
        Bulma (https://bulma.io)
        <br>
        Animate.css (https://animate.style)
        <br>
        confetti.js (https://github.com/mathusummut/confetti.js)`,
        credits: `This yearbook was made using 
        <a href="https://github.com/pablouser1/IberbookEdu" target="_blank">IberbookEdu</a>, distributed under the
        <a href="LICENSE.txt" target="_blank">GPLv3 license</a>`,
        contributors_button: "See contributors",
        madewith: "Made with <span style='color: #e25555;'> &#9829; </span> in Github"
    }
}

// Spanish translations
const lang_es = {
    common: {
        graduation: "Graduación curso"
    },
    tabs: {
        gallery: "Galería",
        about: "Acerca de"
    },
    yearbook: {
        students: "Alumnos",
        teachers: "Profesores",
        video_view: "Ver vídeo",
        video_exit: "Salir"
    },
    gallery: {
        modal_title: "Foto"
    },
    about: {
        attribution: `Este proyecto no sería posible sin la ayuda de los siguientes proyectos
        distribuidos bajo <a href="externalprojects_licenses.txt" target="_blank">las siguientes licencias</a>:<br>
        Bulma (https://bulma.io)
        <br>
        Animate.css (https://animate.style)
        <br>
        confetti.js (https://github.com/mathusummut/confetti.js)`,
        credits: `Este yearbook fue generado usando
        <a href="https://github.com/pablouser1/IberbookEdu" target="_blank">IberbookEdu</a>, distribuido bajo la
        <a href="LICENSE.txt">licencia GPLv3</a>`,
        contributors_button: "Ver contribuidores",
        madewith: "Hecho con <span style='color: #e25555;'> &#9829; </span> en Github"
    }
}


// Multilanguage setup
const allowed_languages = ["es", "en"]
var userLang = (navigator.language || navigator.userLanguage).substring(0,2);

// Set the language to english if the user's language isn't in the allowed_languages
if (!allowed_languages.includes(userLang)){
    console.log("Falling back to English")
    userLang = "en"
}

function translate(){
    // Insert translated text into necessary parts of HTML, not the greatest way of doing this
    // Common
    document.getElementById("hero_subtitle").innerHTML = `${lang["common"]["graduation"]} ${schoolyear_js} 🎉`;
    // Tabs
    document.getElementById("span_gallery").innerHTML = lang["tabs"]["gallery"];
    document.getElementById("span_about").innerHTML = lang["tabs"]["about"];
    // Yearbook
    document.getElementById("teachers_title").innerHTML = lang["yearbook"]["teachers"];
    document.getElementById("students_title").innerHTML = lang["yearbook"]["students"];
    document.getElementById("video_link").innerHTML = lang["yearbook"]["video_view"];
    document.getElementById("video_exit").innerHTML = lang["yearbook"]["video_exit"];
    // Gallery
    document.getElementById("gallery_modal_title").innerHTML = lang["gallery"]["modal_title"];
    // About
    document.getElementById("about_attribution").innerHTML = lang["about"]["attribution"];
    document.getElementById("credits").innerHTML = lang["about"]["credits"];
    document.getElementById("contributors_button").innerHTML = lang["about"]["contributors_button"];
    document.getElementById("contributors_title").innerHTML = lang["about"]["contributors_button"];
    document.getElementById("contributors_footer").innerHTML = lang["about"]["madewith"];
}

function changelanguage(new_lang){
    switch (new_lang){
        case 'en':
            lang = lang_en;
            break;
        case 'es':
            lang = lang_es;
            break;
        default:
            alert("What")
    }
    translate()
}
// Set language and translate document
changelanguage(userLang)
translate()
