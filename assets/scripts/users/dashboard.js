// Yearbook generated
Vue.component('yearbook', {
    props: ["yearbook"],
    template:
    `
    <div>
        <section class='hero is-medium is-success is-bold'>
            <div class='hero-body'>
                <div class='container'>
                    <h1 class='title'>Tu yearbook está listo</h1>
                    <p class='subtitle'>
                        <a href='../yearbooks.php' class='button is-success'>
                            <span class='icon'>
                                <i class='fas fa-eye'></i>
                            </span>
                            <span>Ver</span>
                        </a>
                    </p>
                </div>
            </div>
        </section>
        <hr>
    </div>
    `
})

// Dashboard
Vue.component('dashboard', {
    props: ["user"],
    data() {
        return {
            edit: false,
            type: ""
        }
    },
    template:
    `
    <div class="container">
        <div v-if="!user.photo || !user.video">
            <p class="title">ADVERTENCIA</p>
            <p>Para poder salir en la orla tienes que <strong>subir una foto o un vídeo</strong> como mínimo</p>
            <a href="upload.php" class="button is-success">Subir datos</a>     
            <hr>   
        </div>
        <div v-else-if="!user.link || !user.quote">
            <p class="title">Completa tu perfil</p>
            <p>Todavía puedes subir tu enlace o tu cita</p>
            <a href="upload.php" class="button is-success">Subir datos</a>     
            <hr>  
        </div>
        <div v-if="user.reason" class="notification is-danger">
            <p>Uno o más elementos han sido eliminados por tu administrador, motivo:</p>
            <p>{{ user.reason }}</p>
        </div>
        <p class="title">
            <i class="fas fa-user"></i>
            <span>Tus datos</span>
        </p>
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre completo</th>
                        <th>Foto</th>
                        <th>Vídeo</th>
                        <th>Enlace</th>
                        <th>Cita</th>
                        <th>Fecha de subida</th>
                        <th v-if="user.type == 'P'">Asignatura</th>
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
                        <td>{{ user.link }}</td>
                        <td v-html="user.quote"></td>
                        <td>{{ user.uploaded }}</td>
                        <td v-if="user.type == 'P'">{{ user.subject }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
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
                    <button v-on:click="deleteItems(user.id, type)" class="button is-danger">
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
        <hr>
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
                    alert("Datos eliminados con éxito")
                    window.location.reload()
                }
                else {
                    alert(json_res.description)
                }
            })
        }
    },
    mounted() {
        if (this.user.type == "P") {
            this.type = "teachers"
        }
        else {
            this.type = "students"
        }
    },
})

Vue.component('upload', {
    template:
    `
    <div class="content has-text-centered">
        <h1 class="title">👋 ¡Hola! Bienvenido</h1>
        <p>Parece ser que no tienes datos subidos, puedes comenzar pulsando el botón:</p>
        <a class="button is-info" href="upload.php">
            <span class="icon">
                <i class="fas fa-upload"></i>
            </span>
            <span>Agregar datos</span>
        </a>
        <hr>
    </div>
    `
})

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
            <p class="help">Estos son las fotos y los vídeos de tu curso, estos datos los inserta el administrador de tu grupo</p>
        </div>
    </div>
    `
})

var dashboard_vue = new Vue({
    el: '#main',
    data: {
        user: user_js,
        gallery: gallery_js,
        yearbook: yearbook_js
    }
})
