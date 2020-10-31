// -- Yearbooks expositor handler -- //

// Search bar
Vue.component('search', {
    data() {
        return {
            search: "", // Search text
            searchItem: "schoolyear", // Default
        }
    },
    template:
    `
    <div class="container has-text-centered">
        <label class="label">Buscar</label>
        <div class="field has-addons has-addons-centered">
            <p class="control has-icons-left">
                <input v-model="search" class="input" type="text" placeholder="Filtrar orlas">
                <span class="icon is-left">
                    <i class="fas fa-search"></i>
                </span>
            </p>
            <p class="control">
                <span class="select">
                    <select v-model="searchItem">
                        <option value="schoolyear">Curso</option>
                        <option value="acyear">Año académico</option>
                        <option value="schoolname">Centro escolar</option>
                    </select>
                </span>
            </p>
        </div>
    </div>
    `,
    watch: {
        search: function(searchValue) {
            this.$root.$emit('searchTerm', searchValue, this.searchItem)
        }
    },

})

// Yearbook expositor
Vue.component('yearbooks', {
    props: {
        yearbooks: {
            type: Array,
            requiered: true
        }
    },
    data() {
        return {
            search: "",
            searchItem: "schoolyear",
            yearbook: null // Selected yearbook
        }
    },
    methods: {
        setYearbook: function(yearbook) {
            this.yearbook = yearbook
        }
    },
    computed: {
        searchList() {
          return this.yearbooks.filter(yearbook => {
            return yearbook[this.searchItem].toLowerCase().includes(this.search.toLowerCase())
          })
        }
    },
    mounted() {
        // GET Params and show yearbook directly
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        let id = urlParams.get("id")
        let preview = urlParams.get("preview")
        if (id) {
            this.yearbooks.forEach(yearbook => {
                if (yearbook.id == id) {
                    if (preview) {
                        window.location.href = yearbook.link
                    }
                    this.yearbook = yearbook
                }
            })
            if (!this.yearbook) {
                alert("El ID no es válido")
            }
        }
        this.$root.$on('searchTerm', (search, searchItem) => {
            this.searchItem = searchItem
            this.search = search
        })

        this.$root.$on('closeYearbook', () => {
            this.yearbook = null
        })
    },
    template:
    `
    <div class="container has-text-centered animate__animated animate__fadeIn">
        <div class="columns is-mobile is-centered is-multiline is-vcentered">
            <div class="column is-narrow" v-for="yearbook in searchList">
                <div class="card">
                    <div class="card-image">
                        <figure class="image is-16by9">
                            <img :src="yearbook.banner" alt="Placeholder image">
                        </figure>
                    </div>
                    <div class="card-content">
                        <div class="media">
                            <div class="media-content">
                                <p class="title is-4">{{ yearbook.schoolyear }}</p>
                                <p class="subtitle is-6">{{ yearbook.acyear }}</p>
                            </div>
                        </div>
                        <div class="content">
                            <p>{{ yearbook.schoolname }} - {{ yearbook.votes }} votos</p>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <a v-on:click="setYearbook(yearbook)" class="card-footer-item" target="_blank">Más información</a>
                    </footer>
                </div>
            </div>
        </div>
        <options id="yearbook" v-bind:yearbook="yearbook"></options>
    </div>
    `
})

// Show yearbook
Vue.component('options', {
    props: {
        yearbook: {
            type: Object,
            requiered: true
        }
    },
    data: function () {
        return {
          voted: null,
          url: "",
          shareurl: "",
          whatsappURL: ""
        }
    },
    methods: {
        clipboard: function () {
            document.getElementById("inputlink").select()
            document.execCommand('copy')
            alert("Enlace copiado con éxito")
        },
        // Vote system
        vote: function(id) {
            // Send id and action to do
            fetch(`vote.php?id=${id}`)

            // Get json response
            .then(res => {
                return res.json()
            })
            .then(json_res => {
                // Check if there were any errors
                if (json_res["code"] == "C") {
                    this.voted = id
                    this.yearbook.votes = this.yearbook.votes + 1
                    alert("Has votado con éxito")
                }
                else {
                    alert(`Ha habido un error al procesar tu solicitud, ${json_res.description}`);
                }
            })
        },
        closeYearbook: function() {
            this.$root.$emit('closeYearbook')
        }
    },
    created() {
        // Get website url
        this.voted = voted_js;
    },
    updated() {
        if (this.yearbook) {
            this.url = window.location.href.split('?')[0] + "?id=" + this.yearbook.id + "&preview=true";
            this.shareurl = encodeURIComponent(this.url);
            let starturl;
            if(/Android|iPhone|iPod|BlackBerry|Opera Mini/i.test(navigator.userAgent)){
                // Phone user
                starturl = "whatsapp://send?text="
            }
            else {
                // Desktop user, use Whatsapp Web
                starturl = "https://web.whatsapp.com/send?text="
            }
            this.whatsappURL = `${starturl}Echa%20un%20vistazo%20a%20mi%20anuario%20creado%20con%20IberbookEdu%20en%3A%20${this.shareurl}`
            document.getElementById("yearbook").scrollIntoView({
                behavior: 'smooth'
            })
        }
    },
    template:
    `
    <div v-if="yearbook" class="container has-text-centered animate__animated animate__fadeIn">
        <hr>
        <nav class="level is-mobile">
            <div class="level-left">
                <div class="level-item">
                    <span v-on:click="closeYearbook" class="delete"></span>
                </div>
            </div>
            <div class="level-item">
                <div>
                    <p class="title">
                        <i class="fas fa-book"></i>
                        <span>Yearbook</span>
                    </p>
                    <p>{{ yearbook.schoolyear }}</p>
                </div>
            </div>
            <div class="level-right">
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading">Votos</p>
                        <p class="title">{{ yearbook.votes }}</p>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Opciones básicas -->
        <div class="buttons is-centered">
            <a :href="yearbook.link" target="_blank" class="button is-link">
                <span class="icon">
                    <i class='fas fa-eye'></i>
                </span>
                <span>Ver yearbook</span>
            </a>
            <a :href="yearbook.zip" class="button is-link">
                <span class="icon">
                    <i class="fas fa-file-archive"></i>
                </span>
                <span>Descargar zip</span>
            </a>
            <button :disabled="voted == yearbook.id ? true : false" v-on:click="vote(yearbook.id)" class="button is-primary">
                <span class="icon">
                    <i class="fas fa-star"></i>
                </span>
                <span>Votar</span>
            </button>
        </div>
        <!-- Compartir -->
        <div class="container">
            <div class="field has-addons has-addons-centered">
                <div class="control">
                    <input type="text" id="inputlink" class="input" readonly :value="url">
                </div>
                <div class="control">
                    <button v-on:click="clipboard" class="button is-success">
                        <span class="icon">
                            <i class="fas fa-clipboard"></i>
                        </span>
                        <span>Copiar enlace</span>
                    </button>
                </div>
            </div>
            <br>
            <label class="label">También puedes compartir este yearbook por redes sociales:</label>
            <div class="buttons is-centered">
                <a target='_blank' :href="'https://www.facebook.com/sharer/sharer.php?u=' + shareurl" class="button is-link">
                    <span class="icon">
                        <i class="fab fa-facebook"></i>
                    </span>
                    <span>Facebook</span>
                </a>
                <a target='_blank'
                :href="'https://twitter.com/intent/tweet?text=Echa%20un%20vistazo%20a%20mi%20anuario%20en&url=' + shareurl + '&hashtags=IberbookEdu'"
                class="button is-info">
                    <span class="icon">
                        <i class="fab fa-twitter"></i>
                    </span>
                    <span>Twitter</span>
                </a>
                <a target='_blank' :href="whatsappURL" class="button is-success">
                    <span class="icon">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                    <span>Whatsapp</span>
                </a>
                <a target='_blank' class="button is-link"
                :href="'https://t.me/share/url?url=' + shareurl + '&text=Echa%20un%20vistazo%20a%20mi%20anuario%20creado%20con%20%23IberbookEdu'">
                    <span class="icon">
                        <i class="fab fa-telegram"></i>
                    </span>
                    <span>Telegram</span>
                </a>
            </div>
        </div>
    </div>
    `
})

var yb_vue = new Vue({
    el: '#yearbooks',
    data: {
        showNav: false,
        yearbooks: yearbooks_js
    }
})
