// Users
Vue.component('users', {
    props: ["users", "type"],
    template:
    `
    <div class="container">
        <p v-if="type === 'students'" class="title">
            <i class="fas fa-user-graduate"></i>
            <span>Alumnos</span>
        </p>
        <p v-else class="title">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Profesores</span>
        </p>
        <p class="subtitle">Total: {{ users.length }}</p>
        <div v-if="Object.keys(users).length === 0">
            <p>No hay personas disponibles</p>
        </div>
        <div v-else class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable">
                <thead>
                    <tr>
                        <th>Nombre completo</th>
                        <th>Foto</th>
                        <th>Vídeo</th>
                        <th>Enlace</th>
                        <th>Cita</th>
                        <th>Última modificación</th>
                        <th v-if="type === 'teachers'">Asignatura</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users">
                        <td>{{ user.name }}</td>
                        <td>
                            <a :href="'../getmedia.php?id=' + user.id + '&media=photo&type=' + type" target='_blank'>{{ user.photo }}</a>
                        </td>
                        <td>
                            <a :href="'../getmedia.php?id=' + user.id + '&media=video&type=' + type" target='_blank'>{{ user.video }}</a>
                        </td>
                        <td>
                            <a :disabled="!user.link" :href="user.link" target='_blank' class="button is-small is-link">Abrir enlace</a>
                        </td>
                        <td v-html="user.quote"></td>
                        <td>{{ user.uploaded }}</td>
                        <td v-if="type === 'teachers'">{{ user.subject }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <edit v-bind:users="users" v-bind:type="type"></edit>
    </div>
    `
})

// Edit mode
Vue.component('edit', {
    data() {
        return {
            edit: false
        }
    },
    props: ["users", "type"],
    template:
    `
    <div>
        <div v-if="edit" class="animate__animated animate__fadeIn">
            <p class="title">Eliminar datos</p>
            <div class="field is-grouped">
                <div class="control">
                    <label class="label">Usuario</label>
                    <div class="select">
                        <select id="select_user">
                            <option v-for='user in users' :value='user.id'>{{ user.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="control">
                    <label class="label">Elementos</label>
                    <div class="select is-multiple">
                        <select id="select_items" multiple size="4">
                            <option value="photo">Foto</option>
                            <option value="video">Vídeo</option>
                            <option value="link">Enlace</option>
                            <option value="quote">Cita</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="field">
                <label class="label">Motivo</label>
                <div class="control">
                    <input class="input" id="reason" type="text">
                </div>
            </div>
            <div class="field is-grouped">
                <div class="control">
                    <button v-on:click="edit = false" class="button is-success">
                        <span class="icon">
                            <i class="fas fa-ban"></i>
                        </span>
                        <span>Cancelar</span>
                    </button>
                </div>
                <div class="control">
                    <button v-on:click="deleteItems(type)" class="button is-danger">
                        <span class="icon">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Eliminar</span>
                    </button>
                </div>
            </div>
        </div>
        <div v-else>
            <button v-show="!edit" v-on:click="edit = true" class="button is-danger">
                <span class="icon">
                    <i class="fas fa-edit"></i>
                </span>
                <span>Editar</span>
            </button>
        </div>
    </div>
    `,
    methods: {
        // Delete selected item(s) from user
        deleteItems: function(type) {
            let user = document.getElementById("select_user").options[select_user.selectedIndex].value
            let items = []
            let select = document.getElementById("select_items")
            for (var i = 0; i < select.options.length; i++) {
                opt = select.options[i];
                if (opt.selected) {
                    items.push(opt.value);
                }
            }
            let reason = document.getElementById("reason").value
            let data = new FormData()
            data.append("id", user)
            data.append("type", type)
            data.append("items", items)
            data.append("reason", reason)
            fetch("../helpers/managedata.php", {
                method: "POST",
                body: data
            })
            .then((res) => {
                return res.json()
            })
            .then((json_res) => {
                if (json_res.code == "C") {
                    alert("Datos eliminados con éxito")
                    location.reload()
                }
                else {
                    alert(json_res.description)
                }
            })
        }
    },
})

// Gallery
Vue.component('gallery', {
    props: ["gallery"],
    template:
    `
    <div class="container">
        <p class="title">
            <i class="fas fa-photo-video"></i>
            <span>Galería de tu grupo</span>
        </p>
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable">
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in gallery">
                        <td>
                            <a :href="'../getgallery.php?id=' + item.id" target='_blank'>{{item.name}}</a>
                        </td>
                        <td>{{ item.description }}</td>
                        <td>{{ item.type }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <a class="button is-info" href="gallery.php">
            <span class="icon">
                <i class="fas fa-photo-video"></i>
            </span>
            <span>Modificar galería</span>
        </a>
        <hr>
    </div>
    `
})

// Yearbook options
Vue.component('yearbook', {
    data() {
        return {
            generating: false,
            params: ""
        }
    },
    props: ["yearbook"],
    template:
    `
    <div class="container">
        <div v-if=yearbook.available>
            <h1 class='title'>Yearbook</h1>
            <p class='subtitle'>Generado el {{ yearbook.date }}
            <div class='buttons'>
                <a :href="'../yearbooks.php' + params" target='_blank' class='button is-primary'>
                    <span class='icon'>
                        <i class='fas fa-eye'></i>
                    </span>
                    <span>Ver yearbook</span>
                </a>
                <button v-on:click="deleteYearbook" class='button is-danger'>
                    <span class='icon'>
                        <i class='fas fa-trash'></i>
                    </span>
                    <span>Eliminar yearbook</span>
                </button>
            </div>
        </div>
        <div v-else>
            <p class="title">Administrar yearbook</p>
            <div class="field">
                <label class="label">Plantilla</label>
                <div class="control">
                    <div class="select">
                        <select id="theme_selector">
                            <option v-for="theme in yearbook.themes">{{theme}}</option>
                        </select>
                    </div>
                </div>
                <label class="label">Banner</label>
                <div class="control">
                    <input id="banner" type="file" name="banner" accept="image/jpeg, image/png, image/gif">
                </div>
                <p class="help">Sólo se aceptan jpg, png y gif de máximo 5MB</p>
            </div>
            <div class="buttons">
                <button v-on:click="generateYearbook" v-bind:class="{ 'is-loading': generating}" class="button is-success">
                    <span class="icon">
                        <i class="fas fa-check"></i>
                    </span>
                    <span>Generar Yearbook</span>
                </button>
            </div>
        </div>
    </div>
    `,
    methods: {
        // Generate yearbook with selected options
        generateYearbook: function() {
            // Set loading
            this.generating = true
            document.body.style.cursor = "progress";
            // Get theme
            let theme_select = document.getElementById("theme_selector")
            let theme = theme_select.options[theme_select.selectedIndex].text;
            // Set POST data
            let data = new FormData()
            if (theme === "default") {
                let banner = document.getElementById("banner").files[0]
                data.append('banner', banner)
            }
            // Send id and action to do
            fetch(`yearbook/generate.php?theme=${theme}`, {
                method: "POST",
                body: data
            })
            // Get json response
            .then(res => {
                this.generating = false
                return res.json()
            })
            .then(json_res => {
                alert(json_res["description"])
                document.body.style.cursor = "pointer"; 
                if (json_res["code"] == "C") {
                    // If everyting went ok, reload page
                    location.reload();
                }
            })
        },
        deleteYearbook: function() {
            fetch(`manageyb.php?action=delete`)
            // Get json response
            .then(res => {
                return res.json()
            })
            .then(json_res => {
                alert(json_res["description"])
                if (json_res["code"] == "C") {
                    // If everyting went ok, reset
                    this.yearbook.available = false
                }
            })
        }
    },
    mounted() {
        if (this.yearbook.available) {
            // Set GET params, so when the user clicks, gets redirected to the group's yearbook
            this.params = `?schoolid=${this.yearbook.schoolid}&acyear=${this.yearbook.acyear}&group=${this.yearbook.schoolyear}`
        }
    }
})

// Root instance
var dashboard_vue = new Vue({
    el: '#main',
    data: {
        teachers: teachers_js,
        students: students_js,
        gallery: gallery_js,
        yearbook: yearbook_js
    }
})
