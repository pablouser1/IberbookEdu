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
    <div class="columns is-centered is-multiline is-vcentered fade-in">
        <div v-for="user in users" class="column is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd">
            <div class="card">
                <div class="card-image">
                    <figure @click="$root.videoWatching = user" class="image user-images">
                        <img :src="dataUrl + '/users/' + user.id + '/' + user.photo">
                    </figure>
                </div>
                <div class="card-content">
                    <div class="media">
                        <div class="media-content has-text-centered">
                            <p class="title is-4">
                                <span>{{ user.name }}</span>
                                <span class="tag is-info" v-if="user.type === 'teachers'">{{ user.subject }}</span>
                            </p>
                            <p class="subtitle is-6" >{{ user.surname }}</p>
                        </div>
                    </div>
                    <div class="content">
                        <p>
                            <q v-if="user.quote" v-html="user.quote"></q>
                        </p>
                    </div>
                </div>
                <footer class="card-footer">
                    <a @click="$root.videoWatching = user" class="card-footer-item">Video</a>
                    <a v-if="user.link" :href="user.link" target="_blank" class="card-footer-item">Link</a>
              </footer>
            </div>
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
            const banner = document.getElementById("banner")
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
                    alert("No easter eggs in here")
            }
        }
    },
    created() {
        if (this.ybinfo.banner) {
            document.getElementById("banner").style.background = `url(${dataUrl}/${this.ybinfo.banner})center center`
            document.getElementById("banner").style.backgroundSize = "cover"
        }
        else {
            document.getElementById("banner").classList.replace("has-bg-img", "is-primary")
        }
        document.title = `Yearbook ${this.ybinfo.year}`
    },
    mounted() {
        document.onreadystatechange = () => {
            let splashscreen = document.getElementById("splashscreen")
            splashscreen.classList.add("scale-out-center")
            splashscreen.addEventListener("animationend", () => {
                this.splashscreen = false
            })
        }
    }
})
