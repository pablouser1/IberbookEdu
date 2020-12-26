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
// Users
var users = {
    props: {
        "users": {
            type: Array,
            required: true
        }
    },
    template: 
    `
    <div class="columns is-centered is-multiline is-vcentered animate__animated animate__fadeIn">
        <div v-for="user in users" class="column is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd">
            <article class="media">
                <div class="media-content">
                    <p>
                        <strong>{{user.fullname}}</strong>
                        <figure @click="$root.videoWatching = user" class="image user-images">
                            <img :src="user.photo">
                        </figure>
                        <q v-html="user.quote"></q>
                        <br>
                        <i><small>{{user.date}}</small></i>
                    </p>
                    <span v-if="user.type == 'teachers'" class="tag">{{user.subject}}</span>
                </div>
            </article>
        </div>
    </div>
    `
}

// Gallery
var gallery = {
    props: {
        "gallery": {
            type: Array,
            required: false
        }
    },
    template: 
    `
    <div class="columns is-centered is-multiline is-vcentered animate__animated animate__fadeIn">
        <div v-for="(item) in gallery" class='column is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd'>
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
        "users": users,
        "gallery": gallery,
        "stories": stories
    },
    data: {
        longtimeago: false, // in a galaxy far far away
        teachers: teachers_js, // Teachers data
        students: students_js, // Students data
        gallery: gallery_js, // Gallery data
        ybinfo: ybinfo_js, // General yearbook info
        videoWatching: null,
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
            let splashscreen = document.getElementById("splashscreen")
            splashscreen.classList.add("animate__animated", "animate__fadeOut", "animate__fast")
            splashscreen.addEventListener("animationend", () => {
                this.splashscreen = false
            })
        }
    }
})
