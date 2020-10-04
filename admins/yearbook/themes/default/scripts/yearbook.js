// Changes active tab
function changetab() {
    let hash = window.location.hash.substring(1);
    // Choose tab not hidden (currently active)
    let old_tab = document.querySelector(".tab:not(.is-hidden)")
    let new_tab = document.getElementById(hash);
    if (!new_tab) {
        console.error(`Error while loading ${hash}, that tab doesn't exist`)
    }
    else {
        old_tab.classList.add("is-hidden")
        new_tab.classList.remove("is-hidden")
    }
}

window.addEventListener("hashchange", changetab)

// -- Yearbook -- //

// Teachers
var teachers = {
    props: {
        "teachers": {
            type: Array,
            required: true
        }
    },
    template: 
    `
    <div class="columns is-centered is-multiline is-vcentered is-mobile">
        <div v-for="(teacher) in teachers" class="animate__animated animate__fadeIn column is-half-mobile is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd">
            <article class="media">
                <div class="media-content">
                    <p>
                        <strong>{{teacher.fullname.name}} {{teacher.fullname.surname}}</strong>
                        <a :href="teacher.photo" target="_blank">
                            <figure class="image">
                                <img :src="teacher.photo">
                            </figure>
                        </a>
                        <q v-html="teacher.quote"></q>
                        <br>
                        <i><small>{{teacher.date}}</small></i>
                        <span class="tag">{{teacher.subject}}</span>
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
    `,
    mounted() {
        let stories_prof = setupStories("teachers")
        new Zuck('stories_teachers', {
            story_settings,
            stories: stories_prof,
        });
    }
}

// Students
var students = {
    props: {
        "students": {
            type: Array,
            required: true
        }
    },
    template: 
    `
    <div class="columns is-centered is-multiline is-vcentered is-mobile">
        <div v-for="(student) in students" class="animate__animated animate__fadeIn column is-half-mobile is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd">
            <article class="media">
                <div class="media-content">
                    <p>
                        <strong>{{student.fullname.name}} {{student.fullname.surname}}</strong>
                        <a :href="student.photo" target="_blank">
                            <figure class="image">
                                <img :src="student.photo">
                            </figure>
                        </a>
                        <q v-html="student.quote"></q>
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
    `,
    mounted() {
        let stories_alu = setupStories("students")
        new Zuck('stories_students', {
            story_settings,
            stories: stories_alu,
        });
    }
}

// Gallery
var gallery = {
    props: {
        "gallery": {
            type: Array,
            required: true
        }
    },
    template: 
    `
    <div class="columns is-centered is-multiline is-vcentered is-mobile">
        <div v-for="(item) in gallery" class='animate__animated animate__fadeIn column is-half-mobile is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd'>
            <article class="media">
                <div class="media-content">
                    <a v-if="item.type == 'picture'" :href="item.path" target="_blank">
                        <figure class="image">
                            <img :src="item.path">
                        </figure>
                    </a>
                    <video v-else preload="metadata" controls>
                        <source :src="item.path"></script>
                    </video>
                    <p>{{ item.description }}</p>
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
}

// Vue root
var main = new Vue({
    el: '#main',
    components: {
        "teachers": teachers,
        "students": students,
        "gallery": gallery,
    },
    data: {
        longtimeago: false, // in a galaxy far far away
        teachers: teachers_js, // Teachers data
        students: students_js, // Students data
        gallery: gallery_js, // Gallery data
        ybinfo: ybinfo_js, // General yearbook info
        splashscreen: true,
        ready: false, // Hide splashscreen when everything loads
        showNav: false, // Navbar burger (only mobile/tablet)
        lang: lang // Var in lang.js, language currently used
    },
    methods: {
        enterYearbook: function() {
            let banner = document.getElementById("banner")
            banner.classList.replace("is-fullheight", "is-medium")
            document.getElementById("enterButton").remove()
            this.ready = true
            // Load tab if specified by user
            if (window.location.hash.substring(1)) {
                changetab()
            }
            confetti.start(1500)
        },
        changelang: changelang, // Function in lang.js
        easteregg: function(egg) {
            switch (egg) {
                case "timeago":
                    this.longtimeago = true
                    break;
                default:
                    alert("Aquí no hay ningun easter egg")
                    break;
            }
        }
    },
    created() {
        if (!this.ybinfo.banner) {
            document.getElementById("banner").classList.replace("has-background", "is-primary")
        }
        document.title = `Yearbook ${this.ybinfo.year}`
    },
    mounted() {
        document.onreadystatechange = () => {
            if (document.readyState == "complete") {
                document.getElementById("loading_process").value = 100
                let splashscreen = document.getElementById("splashscreen")
                splashscreen.classList.add("animate__animated", "animate__bounceOut")
                splashscreen.addEventListener('animationend', () => {
                    this.splashscreen = false
                });
            }
          }
          
    }
})
