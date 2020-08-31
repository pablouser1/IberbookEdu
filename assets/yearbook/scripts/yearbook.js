// Changes active tab
window.addEventListener("hashchange", () => {
    var hash = window.location.hash.substring(1);
    if(hash == ""){
        hash = "yearbook"
    }
    // Choose tab not hidden (currently active)
    var old_tab = document.querySelector(".tab:not(.is-hidden)")
    var new_tab = document.getElementById(hash);
    old_tab.classList.add("is-hidden")
    new_tab.classList.remove("is-hidden")
})

// -- Yearbook -- //

// Teachers
Vue.component('teachers', {
    props: {
        "teachers": {
            type: Array,
            required: true
        }
    },
    template: 
    `
    <div class="columns is-centered is-multiline is-vcentered is-mobile">
        <div v-for="(teacher) in teachers" class="column is-half-mobile is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd">
            <article class="media">
                <div class="media-content">
                    <p>
                        <strong>{{teacher.fullname.name}} {{teacher.fullname.surname}}</strong>
                        <span class="tag">{{teacher.subject}}</span>
                        <a :href="teacher.photo" target="_blank">
                            <figure class="image">
                                <img :src="teacher.photo">
                            </figure>
                        </a>
                        <span>{{teacher.quote}}</span>
                        <br>
                        <i><small>{{teacher.date}}</small></i>
                    </p>
                    <nav class="level is-mobile">
                        <div class="level-left">
                            <a class="level-item">
                                <span class="icon is-small"><i class="fas fa-reply"></i></span>
                            </a>
                            <a class="level-item">
                                <span class="icon is-small"><i class="fas fa-retweet"></i></span>
                            </a>
                            <a class="level-item">
                                <span class="icon is-small"><i class="fas fa-heart"></i></span>
                            </a>
                        </div>
                    </nav>
                </div>
            </article>
        </div>
    </div>
    `
})

// Students
Vue.component('students', {
    props: {
        "students": {
            type: Array,
            required: true
        }
    },
    template: 
    `
    <div class="columns is-centered is-multiline is-vcentered is-mobile">
        <div v-for="(student) in students" class="column is-half-mobile is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd">
            <article class="media">
                <div class="media-content">
                    <p>
                        <strong>{{student.fullname.name}} {{student.fullname.surname}}</strong>
                        <a :href="student.photo" target="_blank">
                            <figure class="image">
                                <img :src="student.photo">
                            </figure>
                        </a>
                        <span>{{student.quote}}</span>
                        <br>
                        <i><small>{{student.date}}</small></i>
                    </p>
                    <nav class="level is-mobile">
                        <div class="level-left">
                            <a class="level-item">
                                <span class="icon is-small"><i class="fas fa-reply"></i></span>
                            </a>
                            <a class="level-item">
                                <span class="icon is-small"><i class="fas fa-retweet"></i></span>
                            </a>
                            <a class="level-item">
                                <span class="icon is-small"><i class="fas fa-heart"></i></span>
                            </a>
                        </div>
                    </nav>
                </div>
            </article>
        </div>
    </div>
    `
})

// Gallery
Vue.component('gallery', {
    props: {
        "gallery": {
            type: Array,
            required: true
        }
    },
    template: 
    `
    <div class="columns is-centered is-multiline is-vcentered is-mobile">
        <div v-for="(pic) in gallery" class='column is-half-mobile is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd'>
            <div class='container'>
                <div class='card'>
                    <div class="card-image">
                        <a :href="pic.path" target="_blank">
                            <figure class='image'>
                                <img :src='pic.path'>
                            </figure>
                        </a>
                    </div>
                    <div class="content">
                        {{pic.description}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    `
})

// Vue root
var main = new Vue({
    el: '#main',
    data: {
        longtimeago: false, // in a galaxy far far away
        theme: false,
        teachers: teachers_js, // Teachers data
        students: students_js, // Students data
        gallery: gallery_js, // Gallery data
        ready: false, // Toggle splashscreen when everything loads
        showNav: false, // Navbar burger (only mobile/tablet)
        lang: lang // Var in lang.js, language currently used
    },
    methods: {
        // Light/Dark theme
        toggletheme: function() {
            let darkLink = document.getElementById('dark-theme');
            if(darkLink){
                darkLink.remove();
            }else{
                darkLink = document.createElement('link');
                darkLink.rel = 'stylesheet';
                darkLink.id = 'dark-theme';
                darkLink.href = 'styles/vendor/bulma-prefers-dark.min.css'
                document.head.appendChild(darkLink);
            }
        },
        changelang: changelang, // Function in lang.js
        easteregg: function (egg) {
            switch (egg) {
                case "timeago":
                    this.longtimeago = true
                    break;
                default:
                    alert("¡Hola!")
                    break;
            }
        }
    },
    mounted: function() {
        // Show yearbook if everything already loaded
        this.$nextTick(function () {
            this.ready = true
            confetti.start(1000)
        })
    }
})

// -- About -- //
var contributors_modal = document.getElementById("contributors_modal")

function contributors_open() {
    contributors_modal.classList.add("is-active")
}

function contributors_close() {
    contributors_modal.classList.remove("is-active");
}
