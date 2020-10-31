// Yearbook generated
Vue.component('mainmenu', {
    data() {
        return {
            params: ""
        }
    },
    props: ["user", "yearbook"],
    template:
    `
    <div>
        <!-- Yearbook ready -->
        <section v-if="yearbook.available" class='hero is-medium is-success is-bold'>
            <div class='hero-body'>
                <div class='container'>
                    <h1 class='title'>Tu yearbook está listo</h1>
                    <p class='subtitle'>
                        <a :href="'../yearbooks.php' + params" class='button is-success'>
                            <span class='icon'>
                                <i class='fas fa-eye'></i>
                            </span>
                            <span>Ver</span>
                        </a>
                    </p>
                </div>
            </div>
        </section>
        <!-- More info -->
        <div v-else>
            <div class="columns is-vcentered">
                <div v-if="user.reason" class="box column is-narrow">
                    <p>Uno o más elementos han sido eliminados por tu administrador, motivo:</p>
                    <p>{{ user.reason }}</p>
                </div>
                <div v-if="!user.photo || !user.video" class="box column is-narrow">
                    <p class="title">ADVERTENCIA</p>
                    <p>Para poder salir en la orla tienes que <strong>subir una foto o un vídeo</strong> como mínimo</p>
                    <a href="upload.php" class="button is-success">Subir datos</a>   
                </div>
                <div v-else-if="!user.link || !user.quote" class="box column is-narrow">
                    <p class="title">Completa tu perfil</p>
                    <p>Todavía puedes subir tu enlace o tu cita</p>
                    <a href="upload.php" class="button is-success">Subir datos</a>    
                </div>
                <div v-else class="box column is-narrow">
                    Todo listo
                </div>
            </div>
        </div>
    </div>
    `,
    mounted() {
        // Set GET params, so when the user clicks, gets redirected to the group's yearbook
        this.params = `?id=${this.yearbook.id}`
    }
})

Vue.component('edit', {
    data() {
        return {
            edit: false
        }
    },
    props: ["user"],
    template:
    `
    <div>
        <div v-show="edit">
            <div class="field">
                <label class="label">Elige los datos</label>
                <div class="control">
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
                    <button v-on:click="deleteItems(user.id, user.type)" class="button is-danger">
                        <span class="icon">
                            <i class="fas fa-trash"></i>
                        </span>
                        <span>Eliminar</span>
                    </button>
                </div>
            </div>
        </div>
        <button v-show="!edit" v-on:click="edit = true" class="button is-danger">
            <span class="icon">
                <i class="fas fa-edit"></i>
            </span>
            <span>Editar</span>
        </button>
    </div>
    `,
    methods: {
        deleteItems: function(id, type) {
            let values = []
            let select = document.getElementById("select_items")
            for (var i = 0; i < select.options.length; i++) {
                opt = select.options[i];
                if (opt.selected) {
                    values.push(opt.value);
                }
            }
            let data = new FormData()
            data.append("id", id)
            data.append("type", type)
            data.append("items", values)

            fetch("../helpers/managedata.php", {
                method: "POST",
                body: data
            })
            // Get json response
            .then(res => {
                return res.json()
            })
            .then(json_res => {
                if (json_res.code == "C") {
                    this.edit = false
                    alert("Datos eliminados con éxito")
                    values.forEach(item => {
                        // Reset deleted items
                        this.user[item] = null
                    })
                }
                else {
                    alert(json_res.description)
                }
            })
        }
    }
})


// Dashboard
Vue.component('user', {
    props: ["user"],
    template:
    `
    <div>
        <p class="title">
            <i class="fas fa-user"></i>
            <span>Tus datos</span>
        </p>
        <div class="table-container">
            <table class="table is-striped is-narrow is-hoverable is-fullwidth">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre completo</th>
                        <th>Foto</th>
                        <th>Vídeo</th>
                        <th>Enlace</th>
                        <th>Cita</th>
                        <th>Última modificación</th>
                        <th v-if="user.type == 'teachers'">Asignatura</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ user.id }}</td>
                        <td>{{ user.fullname }}</td>
                        <td>
                            <a :href="'../getmedia.php?id=' + user.id + '&media=photo&type=' + user.type" target="_blank">{{ user.photo }}</a>
                        </td>
                        <td>
                            <a :href="'../getmedia.php?id=' + user.id + '&media=video&type=' + user.type" target="_blank">{{ user.video }}</a>
                        </td>
                        <td>
                            <a :disabled="!user.link" :href="user.link" target='_blank' class="button is-small is-link">Abrir enlace</a>
                        </td>
                        <td v-html="user.quote"></td>
                        <td>{{ user.uploaded }}</td>
                        <td v-if="user.type === 'teachers'">{{ user.subject }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <edit v-bind:user="user"></edit>
    </div>
    `
})

Vue.component('gallery', {
    props: ["gallery"],
    template:
    `
    <div>
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
            <p class="help">Estos son las fotos y los vídeos de tu curso, estos datos los inserta el administrador de tu grupo</p>
        </div>
    </div>
    `
})

var dashboard_vue = new Vue({
    el: '#main',
    data: {
        showNav: false,
        tab: "mainmenu",
        user: user_js,
        gallery: gallery_js,
        yearbook: yearbook_js
    },
    methods: {
        changeTab: function(tab) {
            this.showNav = false
            this.tab = tab
            document.getElementById("items").scrollIntoView({
                behavior: 'smooth'
            })
        }
    }
})
