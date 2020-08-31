// -- Translations -- //

// Common
const common = {
    credits: `
    Bulma (https://bulma.io)
    <br>
    bulma-prefers-dark (https://github.com/jloh/bulma-prefers-dark)
    <br>
    Vue.js (https://vuejs.org)
    <br>
    Animate.css (https://animate.style)
    <br>
    zuck.js (https://github.com/ramon82/zuck.js)
    <br>
    confetti.js (https://github.com/mathusummut/confetti.js)
    `
}

const translations = {
    en: {
        common,
        banner: {
            title: "Graduation"
        },
        tabs: {
            gallery: "Gallery",
            about: "About",
            theme: "Change theme"
        },
        yearbook: {
            students: "Students",
            teachers: "Teachers",
        },
        about: {
            attribution: `This project wouldn't be possible without the help of the following projects 
            distributed under <a href="externalprojects_licenses.txt" target="_blank">the following licenses</a>:<br>
            ${common.credits}`,
            credits: `This yearbook was made using 
            <a href="https://github.com/pablouser1/IberbookEdu" target="_blank">IberbookEdu</a>, an open source project
            distributed under the <a href="LICENSE.html" target="_blank">AGPLv3 license</a>`
        },
        footer: {
            madewith: "Made with <span style='color: #e25555;'> &#9829; </span> in Github"
        },
        misc: {
            longtime: "Hi!👋 Long time no see, how is everything going?"
        }
    },
    es: {
        common,
        banner: {
            title: "Graduación"
        },
        tabs: {
            gallery: "Galería",
            about: "Acerca de",
            theme: "Cambiar tema"
        },
        yearbook: {
            students: "Alumnos",
            teachers: "Profesores",
        },
        about: {
            attribution: `Este proyecto no sería posible sin la ayuda de los siguientes proyectos
            distribuidos bajo <a href="externalprojects_licenses.txt" target="_blank">las siguientes licencias</a>:<br>
            ${common.credits}`,
            credits: `Este yearbook fue generado usando
            <a href="https://github.com/pablouser1/IberbookEdu" target="_blank">IberbookEdu</a>, un proyecto de código abierto 
            distribuido bajo la <a href="LICENSE.html" target="_blank">licencia AGPLv3</a>`
        },
        footer: {
            madewith: "Hecho con <span style='color: #e25555;'> &#9829; </span> en Github"
        },
        misc: {
            longtime: "¡Hola!👋 Hace más de cinco años de esta graduación, ¿qué tal va todo?"
        }
    }
  }

// Multilanguage setup
var lang;
const allowed_languages = ["es", "en"]
var userLang = (navigator.language || navigator.userLanguage).substring(0,2)

// Set the language to english if the user's language isn't in the allowed_languages
if (!allowed_languages.includes(userLang)){
    console.log("Falling back to English")
    lang = translations.en;
}
else {
    lang = translations[userLang];
}

function changelang(lang) {
    main.lang = translations[lang]
}
