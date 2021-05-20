// -- Translations -- //

// Common
const translate_common = {
    external: `
    <ul>
        <li>Bulma (<a href="https://bulma.io">web</a>)</li>
        <li>Vue.js (<a href="https://vuejs.org">web</a>)</li>
        <li>Animista (<a href="https://animista.net">web</a>)</li>
        <li>Confetti.js (<a href="https://github.com/mathusummut/confetti.js">web</a>)</li>
        <li>Spinkit (<a href="https://github.com/tobiasahlin/SpinKit">web</a>)</li>
    </ul>
    `
}

const urls = {
    external: `${themeUrl}/licenses/externalProjects.html`,
    project: `"${commonUrl}/licenses/IberbookEdu.html`
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
            distributed under <a href="${urls.external}" target="_blank">the following licenses</a>:<br>
            ${translate_common.external}`,
            credits: `This yearbook was made using
            <a href="https://github.com/pablouser1/IberbookEdu-backend" target="_blank">IberbookEdu</a>, an open source project
            distributed under the <a href="${urls.project}" target="_blank">AGPLv3 license</a>`
        },
        footer: {
            madewith: "Made with <span style='color: #e25555;'> &#9829; </span> using <a href='https://twitter.com/search?q=%23IberbookEdu' target='_blank'>#IberbookEdu</a>"
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
            distribuidos bajo <a href="${urls.external}" target="_blank">las siguientes licencias</a>:<br>
            ${translate_common.external}`,
            credits: `Esta orla fue generada usando
            <a href="https://github.com/pablouser1/IberbookEdu-backend" target="_blank">IberbookEdu</a>, un proyecto de código abierto
            distribuido bajo la <a href="${urls.project}" target="_blank">licencia AGPLv3</a>`
        },
        footer: {
            madewith: "Hecho con <span style='color: #e25555;'> &#9829; </span> usando <a href='https://twitter.com/search?q=%23IberbookEdu' target='_blank'>#IberbookEdu</a>"
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
            distribués sous <a href="${urls.external}">les licences suivantes</a>:<br>
            ${translate_common.external}`,
            credits: `Cet annuaire a été généré en utilisant
            <a href="https://github.com/pablouser1/IberbookEdu-backend" target="_blank">IberbookEdu</a>, un projet open source
            distribué sous la <a href="${urls.project}" target="_blank">licence AGPLv3</a>`
        },
        footer: {
            madewith: "Fait avec <span style='color: #e25555;'> &#9829; </span> în <a href='https://twitter.com/search?q=%23IberbookEdu' target='_blank'>#IberbookEdu</a>"
        },
        misc: {
            longtime: "Salut!👋 Cela fait plus de cinq ans depuis cette graduation, comment ça va?"
        }
    }
  }

// Multilanguage setup

// Default english
var lang = translations.en;
const allowed_languages = ["en", "es", "fr"]
var userLang = (navigator.language || navigator.userLanguage).substring(0,2)

// Set the language to english if the user's language isn't in the allowed_languages
if (allowed_languages.includes(userLang)){
    lang = translations[userLang];
}

function changelang(lang) {
    main.lang = translations[lang]
}
