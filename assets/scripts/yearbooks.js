var getUrl = window.location;
var baseUrl = getUrl .protocol + "//" + getUrl.host;

Vue.component('schools', {
    props: {
        "schools": {
            requiered: true
        }
    },
    template:
    `
    <div class="container">
        <p class="title has-text-centered">
            <span class="icon">
                <i class="fas fa-school"></i>
            </span>
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
            required: true
        },
        "groupsextra": {
            requiered: true
        }
    },
    template: 
    `
    <div v-if="!groups" class="container has-text-centered">
        <p class="title">Cursos</p>
        <p class="subtitle">Elige un centro y curso académico...</p>
    </div>
    
    <div v-else-if="groups" class="container">
        <p class="title has-text-centered">Cursos</p>
        <p class="subtitle has-text-centered">{{ groupsextra.schoolname }} {{groupsextra.acyear }}</p>
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
            required: true
        },
        "yearbookextra": {
            requiered: true
        }
    },
    template:
    `
    <div v-if="!yearbook" class="container has-text-centered">
        <p class="title">Yearbook</p>
        <p class="subtitle">Elige un curso...</p>
    </div>

    <div v-else-if="yearbook" class="container has-text-centered animate__animated animate__fadeIn">
        <p class="title">Yearbook</p>
        <p class="subtitle">{{ yearbookextra.groupname }}</p>
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
        <div class="container">
            <span>También puedes compartir este yearbook por redes sociales:</span>
            <div class="buttons is-centered">
                <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURI($root.url + yearbook.link)" class="button is-link">
                    <span class="icon">
                        <i class="fab fa-facebook"></i>
                    </span>
                    <span>Facebook</span>
                </a>
                <a
                :href="'https://twitter.com/intent/tweet?text=Echa%20un%20vistazo%20a%20mi%20anuario%20creando%20con%20%23IberbookEdu%20en%3A%20' + $root.url + yearbook.link"
                class="button is-info">
                    <span class="icon">
                        <i class="fab fa-twitter"></i>
                    </span>
                    <span>Twitter</span>
                </a>
                <a
                :href="'whatsapp://send?text=Echa%20un%20vistazo%20a%20mi%20anuario%20creado%20con%20%23IberbookEdu%20en%3A%20' + $root.url + yearbook.link"
                data-action="share/whatsapp/share"
                class="button is-success">
                    <span class="icon">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                    <span>Whatsapp</span>
                </a>
                <a
                :href="'https://t.me/share/url?url=' + $root.url + yearbook.link + '&text=Echa%20un%20vistazo%20a%20mi%20anuario%20creado%20con%20%23IberbookEdu'"
                class="button is-link">
                    <span class="icon">
                        <i class="fab fa-telegram"></i>
                    </span>
                    <span>Telegram</span>
                </a>
            </div>
            <div class="container">
                <div id="qrcode"></div>
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
                this.setgroup(yearbooks_js[schoolid]["acyears"][acyear])

                // If group is in object
                if (group in yearbooks_js[schoolid]["acyears"][acyear]) {
                    this.showyearbook(yearbooks_js[schoolid]["acyears"][acyear][group])
                }
            }
        }
    },
})

// Check if user has predifined yearbook (GET Parameter)

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
          tmp = item.split("=");
          if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        });
    return result;
}

var schoolid = findGetParameter("schoolid")
var acyear = findGetParameter("acyear")
var group = findGetParameter("group")

// Show yearbook info directly
if (schoolid && acyear && group) {
    yb_vue.quicktravel(schoolid, acyear, group)
}
