var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host;

Vue.component('schools', {
    props: {
        "schools": {
            type: Array,
            requiered: true
        }
    },
    template:
    `
    <div class="container">
        <p class="title has-text-centered">
            <i class="fas fa-school"></i>
            <span>Centros</span>
        </p>
        <p class="subtitle has-text-centered">
            <span v-if="Object.keys(schools).length === 0">No hay ningún centro con yearbooks disponible</span>
            <span v-else>Elige un centro</span>
        </p>
        <div class="columns is-mobile is-centered is-multiline is-vcentered animate__animated animate__zoomIn">
            <div class="column is-narrow" v-for="(school, schoolid, acyears) in schools">
                <nav class="panel">
                    <p class="panel-heading">
                        <span class="icon">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <span>{{school.schoolname}} - {{schoolid}}</span>
                    </p>
                    <p class="panel-tabs"></p>
                    <a v-on:click="$root.setgroup(group, school.schoolname, year)" class="panel-block" v-for="(group, year) in school.acyears">
                        <span class="panel-icon">
                            <i class="fas fa-calendar" aria_hidden="true"></i>
                        </span>
                        <span>Curso académico {{year}}</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
    `
})

Vue.component('groups', {
    props: {
        "groups": {
            type: Array,
            required: true
        },
        "groupsextra": {
            type: Array,
            requiered: true
        }
    },
    template: 
    `
    <div v-if="!groups" class="container has-text-centered">
        <p class="title has-text-centered">
            <i class="fas fa-users"></i>
            <span>Cursos</span>
        </p>
        <p class="subtitle">Elige un centro y curso académico...</p>
    </div>
    
    <div v-else-if="groups" class="container">
        <p class="title has-text-centered">
            <i class="fas fa-users"></i>
            <span>Cursos</span>
        </p>
        <p class="subtitle has-text-centered">{{ groupsextra.schoolname }} / {{groupsextra.acyear }}</p>
        <div class="columns is-mobile is-centered is-vcentered is-multiline">
            <div class="column is-narrow animate__animated animate__fadeIn" v-for="(group, index) in groups">
                <button @click="$root.showyearbook(group, index)" class="button is-success">{{ index }}</button>
            </div>
        </div>
    </div>
    `
})

Vue.component('yearbook', {
    props: {
        "yearbook": {
            type: Array,
            required: true
        },
        "yearbookextra": {
            type: Array,
            requiered: true
        }
    },
    methods: {
        clipboard: function () {
            document.getElementById("inputlink").select()
            document.execCommand('copy')
            alert("Enlace copiado con éxito")
        }
    },
    template:
    `
    <div v-if="!yearbook" class="container has-text-centered">
        <p class="title">
            <i class="fas fa-book"></i>
            <span>Yearbook</span>
        </p>
        <p class="subtitle">Elige un curso...</p>
    </div>

    <div v-else-if="yearbook" class="container has-text-centered animate__animated animate__fadeIn">
        <p class="title">
            <i class="fas fa-book"></i>
            <span>Yearbook</span>
        </p>
        <p class="subtitle">{{ yearbookextra.groupname }}</p>
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
        </div>
        <!-- Compartir -->
        <div class="container">
            <div class="field has-addons has-addons-centered">
                <div class="control">
                    <input type="text" id="inputlink" class="input" readonly :value="encodeURI($root.url + yearbook.link)">
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
                <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURI($root.url + yearbook.link)" class="button is-link">
                    <span class="icon">
                        <i class="fab fa-facebook"></i>
                    </span>
                    <span>Facebook</span>
                </a>
                <a
                :href="'https://twitter.com/intent/tweet?text=Echa%20un%20vistazo%20a%20mi%20anuario%20creando%20con%20IberbookEdu&url=' + encodeURI($root.url + yearbook.link) + '&hashtags=IberbookEdu'"
                class="button is-info">
                    <span class="icon">
                        <i class="fab fa-twitter"></i>
                    </span>
                    <span>Twitter</span>
                </a>
                <a
                :href="'whatsapp://send?text=Echa%20un%20vistazo%20a%20mi%20anuario%20creado%20con%20berbookEdu%20en%3A%20' + $root.url + yearbook.link"
                data-action="share/whatsapp/share"
                class="button is-success">
                    <span class="icon">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                    <span>Whatsapp</span>
                </a>
                <a
                :href="'https://t.me/share/url?url=' + encodeURI($root.url + yearbook.link) + '&text=Echa%20un%20vistazo%20a%20mi%20anuario%20creado%20con%20%23IberbookEdu'"
                class="button is-link">
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
      schools: yearbooks_js,
      groups: null,
      groupsextra: null,
      yearbook: null,
      yearbookextra: null,
      url: baseUrl,
    },
    methods: {
        setgroup: function(groups, schoolname, acyear) {
            // Used for resetting
            this.yearbook = null
            // See available groups
            this.groupsextra = {
                schoolname: schoolname,
                acyear: acyear
            }
            this.groups = groups
        },
        showyearbook: function(yearbook, groupname) {
            this.yearbookextra = {
                groupname: groupname
            }
            this.yearbook = yearbook
        },
        quicktravel: function(schoolid, acyear, group) {
            // If schoolid and academic year are in object
            if ( (schoolid in yearbooks_js) && (acyear in yearbooks_js[schoolid]["acyears"]) ) {
                this.setgroup(yearbooks_js[schoolid]["acyears"][acyear], yearbooks_js[schoolid]["schoolname"], acyear)

                // If group is in object
                if (group in yearbooks_js[schoolid]["acyears"][acyear]) {
                    this.showyearbook(yearbooks_js[schoolid]["acyears"][acyear][group], group)
                }
            }
        }
    },
    mounted() {
        // Check if user has predifined yearbook (GET Parameter)
        let uri = window.location.search.substring(1);
        if (uri !== "") {
            let params = new URLSearchParams(uri);
            let schoolid = params.get("schoolid")
            let acyear = params.get("acyear")
            let group = params.get("group")
            // Show yearbook info directly
            if (schoolid && acyear && group) {
                this.quicktravel(schoolid, acyear, group)
            }
        }
    }
})
