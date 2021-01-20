Vue.component('mainmenu', {
    props: ["staff", "schools"],
    template:
    `
    <div>
        <div class="columns is-mobile is-vcentered">
            <div class="column is-narrow">
                <p class="title is-4">
                    <i class="fas fa-user-shield"></i>
                    <span>Staff</span>
                </p>
                <table class="table is-bordered is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre de usuario</th>
                            <th>Permisos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in staff">
                            <td>{{ user.id }}</td>
                            <td>{{ user.username }}</td>
                            <td>{{ user.permissions }}</td>
                        </tr>
                    </tbody>
                </table>
                <a href="staff.php" class="button is-info">
                    <span class="icon">
                        <i class="fas fa-user-friends"></i>
                    </span>
                    <span>Add/remove staff</span>
                </a>
            </div>
            <div class="column is-narrow">
                <p class="title is-4">
                    <i class="fas fa-school"></i>
                    <span>Schools</span>
                </p>
                <table class="table is-bordered is-striped is-hoverable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="school in schools">
                            <td>{{ school.id }}</td>
                            <td>{{ school.name }}</td>
                        </tr>
                    </tbody>
                </table>
                <!-- TODO. MAKE MODAL FOR SCHOOL ADD/REMOVE -->
                <button id="manageschool" type="button" class="button is-info">
                    <span class="icon">
                        <i class="fas fa-school"></i>
                    </span>
                    <span>Add/remove schools</span>
                </button> 
            </div>
        </div>
    </div>
    `
})

Vue.component('users', {
    props: ["schools"],
    data() {
        return {
            schoolid: "",
            schoolyear: "",
            users: null,
            gallery: null
        }
    },
    template:
    `
    <div>
        <div class="field">
            <label class="label">School ID</label>
            <div class="control">
                <div class="select">
                    <select v-model="schoolid">
                        <option v-for="school in schools" :value="school.id">{{ school.id }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field">
            <label class="label">School year</label>
            <div class="control">
                <input v-model="schoolyear" class="input" type="text" placeholder="4º ESO A">
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button v-on:click="getUsers" type="button" class="button is-success">Ver</button>
            </div>
        </div>
        <div v-if="users">
            <div class="table-container">
                <table class="table is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full name</th>
                            <th>Photo</th>
                            <th>Video</th>
                            <th>Link</th>
                            <th>Quote</th>
                            <th>Last modified</th>
                            <th>Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users">
                            <td>{{ user.id }}</td>
                            <td>{{ user.name }}</td>
                            <td>
                                <a :href="'../../users/getmedia.php?id=' + user.id + '&media=photo&type=' + user.type" target='_blank'>{{ user.photo }}</a>
                            </td>
                            <td>
                                <a :href="'../../users/getmedia.php?id=' + user.id + '&media=video&type=' + user.type" target='_blank'>{{ user.video }}</a>
                            </td>
                            <td>
                                <a :disabled="!user.link" :href="user.link" target='_blank' class="button is-small is-link">Abrir enlace</a>
                            </td>
                            <td v-html="user.quote"></td>
                            <td>{{ user.uploaded }}</td>
                            <td>{{ user.subject }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                                <a :href="'../../gallery/getgallery.php?id=' + item.id" target='_blank'>{{item.name}}</a>
                            </td>
                            <td>{{ item.description }}</td>
                            <td>{{ item.type }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    `,
    methods: {
        getUsers: function() {
            fetch(`getprivinfo.php?schoolid=${this.schoolid}&schoolyear=${this.schoolyear}`)
            .then((res) => {
                return res.json()
            })
            .then((json_res) => {
                if (json_res.code == "C") {
                    this.users = json_res.data.users
                    this.gallery = json_res.data.gallery
                }
            })
        }
    }
})

// Root instance
var dashboard_vue = new Vue({
    el: '#main',
    data: {
        showNav: false,
        tab: "mainmenu",
        staff: staff_js,
        schools: schools_js
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
