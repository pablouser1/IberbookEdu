// -- Translations -- //

// Common
const translate_common = {
    external: `
    <ul>
        <li>Bulma (<a href="https://bulma.io">web</a>)</li>
        <li>Vue.js (<a href="https://vuejs.org">web</a>)</li>
        <li>Animate.css (<a href="https://animate.style">web</a>)</li>
        <li>Zuck.js (<a href="https://github.com/ramon82/zuck.js">web</a>)</li>
        <li>Confetti.js (<a href="https://github.com/mathusummut/confetti.js">web</a>)</li>
        <li>Spinkit (<a href="https://github.com/tobiasahlin/SpinKit">web</a>)</li>
    </ul>
    `
}

const translations = {
    // English
    en: {
        splashscreen: {
            loading: "Loading..."
        },
        banner: {
            title: "Graduation"
        },
        tabs: {
            gallery: "Gallery",
            about: "About",
        },
        yearbook: {
            students: "Students",
            teachers: "Teachers",
        },
        about: {
            attribution: `This project wouldn't be possible without the help of the following projects 
            distributed under <a href="externalprojects.html" target="_blank">the following licenses</a>:<br>
            ${translate_common.external}`,
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
    // Spanish
    es: {
        splashscreen: {
            loading: "Cargando..."
        },
        banner: {
            title: "Graduación"
        },
        tabs: {
            gallery: "Galería",
            about: "Acerca de",
        },
        yearbook: {
            students: "Alumnos",
            teachers: "Profesores",
        },
        about: {
            attribution: `Este proyecto no sería posible sin la ayuda de los siguientes proyectos
            distribuidos bajo <a href="externalprojects.html" target="_blank">las siguientes licencias</a>:<br>
            ${translate_common.external}`,
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
    },
    // French
    fr: {
        splashscreen: {
            loading: "Chargement..."
        },
        banner: {
            title: "Graduation"
        },
        tabs: {
            gallery: "Galerie",
            about: "About"
        },
        yearbook: {
            students: "Élèves",
            teachers: "Professeures"
        },
        about: {
            attribution: `Ce projet ne serait pas possible sans l'aide des projets suivants
            distribués sous <a href="externalprojects.html">les licences suivantes</a>:<br>
            ${translate_common.external}`,
            credits: `Cet annuaire a été généré en utilisant
            <a href="https://github.com/pablouser1/IberbookEdu" target="_blank">IberbookEdu</a>, un projet open source
            distribué sous la <a href="LICENSE.html" target="_blank">licence AGPLv3</a>`
        },
        footer: {
            madewith: "Fait avec <span style='color: #e25555;'> &#9829; </span> în Github"
        },
        misc: {
            longtime: "Salut!👋 Cela fait plus de cinq ans depuis cette graduation, comment ça va?"
        }
    }
  }

// Multilanguage setup
var lang;
const allowed_languages = ["en", "es", "fr"]
var userLang = (navigator.language || navigator.userLanguage).substring(0,2)

// Set the language to english if the user's language isn't in the allowed_languages
if (!allowed_languages.includes(userLang)){
    console.warn("Falling back to English")
    lang = translations.en;
}
else {
    lang = translations[userLang];
}

function changelang(lang) {
    main.lang = translations[lang]
}
