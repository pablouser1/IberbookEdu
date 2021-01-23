Vue.component("database", {
    template:
    `
    <div class="section">
        <h1 class="title">Database</h1>
        <hr>
        <div class="field">
            <label class="label">Database name</label>
            <div class="control">
                <input name="db[name]" class="input" type="text" placeholder="Example: iberbook_db" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Host</label>
            <div class="control">
                <input name="db[host]" class="input" type="text" placeholder="Ej: localhost" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Port</label>
            <div class="control">
                <input name="db[port]" class="input" type="text" value="3306" required>
            </div>
            <p class="help">Por detecto es 3306</p>
        </div>
        <div class="field">
            <label class="label">Username</label>
            <div class="control">
                <input name="db[username]" class="input" type="text" placeholder="Ej: user" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input name="db[password]" class="input" type="password" placeholder="**********" required>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button v-on:click="$root.stage = 'owner'" type="button" class="button is-success">
                    <span class="icon">
                        <i class="fas fa-forward"></i>
                    </span>
                    <span>Next</span>
                </button>
            </div>
        </div>
    </div>
    `
})

Vue.component("owner", {
    template:
    `
    <div class="section">
        <h1 class="title">Server details</h1>
        <h2 class="title">Owner's account</h2>
        <h2 class="subtitle">This account will have full permissions on the instance</h2>
        <div class="field">
            <label class="label">Username</label>
            <div class="control">
                <input name="owner[username]" class="input" type="text" placeholder="Ej: user" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Password
            </label>
            <div class="control">
                <input name="owner[password]" class="input" type="password" placeholder="***********" required>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button v-on:click="$root.stage = 'database'" type="button" class="button is-info">
                    <span class="icon">
                        <i class="fas fa-backward"></i>
                    </span>
                    <span>Back</span>
                </button>
            </div>
            <div class="control">
                <button v-on:click="$root.stage = 'server'" type="button" class="button is-success">
                    <span class="icon">
                        <i class="fas fa-forward"></i>
                    </span>
                    <span>Next</span>
                </button>
            </div>
        </div>
    </div>
    `
})

Vue.component("server", {
    props: {
        "dirs": {
            type: Object
        }
    },
    data() {
        return {
            loginSystem: "local"
        }
    },
    template:
    `
    <div class="section">
        <h1 class="title">Server details</h1>
        <h2 class="title">Options</h2>
        <div class="field">
            <label class="label">Choose your login system</label>
            <div class="control">
                <div class="select">
                    <select v-model="loginSystem" name="global[login]">
                        <option value="local">Local Database</option>
                        <option value="andalucia">PASEN/SENECA (Andalucía)</option>
                        <option value="madrid">ROBLE (Madrid)</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field">
            <label class="label">Uploads directory</label>
            <div class="control">
                <input name="global[uploaddir]" class="input" type="text" :value="dirs.upload" required>
            </div>
            <p class="help">Stores users' photos and videos, HIGHLY recommended to be a private directory</p>
        </div>
        <div class="field">
            <h2 class="title">Schools</h2>
            <label class="label">School's ID</label>
            <div class="control">
                <input name="schoolinfo[id]" class="input" type="number" placeholder="Ej: 181206713" required>
            </div>
            <p v-if="loginSystem == 'andalucia' || 'madrid'"><p class="help">Puedes conseguir la información necesaria <a href="https://www.juntadeandalucia.es/educacion/vscripts/centros/index.asp" target="_blank">aquí</a> (Andalucia) o <a href="https://www.madrid.org/wpad_pub/run/j/MostrarConsultaGeneral.icm" target="_blank">aquí</a> (Madrid)</p></p>
            <label class="label">Name</label>
            <div class="control">
                <input name="schoolinfo[name]" class="input" type="text">
            </div>
        </div>
        <h2 class="title">Allowed frontend servers</h2>
        <button v-on:click="addFrontend" type="button" class="button">Add fronteend</button>
        <div id="frontends">
            <div class="field">
                <div class="control">
                    <input name="frontends[]" class="input" type="text" required value="https://iberbookedu.onrender.com">
                </div>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button v-on:click="$root.stage = 'owner'" type="button" class="button is-info">
                    <span class="icon">
                        <i class="fas fa-backward"></i>
                    </span>
                    <span>Back</span>
                </button>
            </div>
            <div class="control">
                <button type="submit" class="button is-success">
                    <span class="icon">
                        <i class="fas fa-paper-plane"></i>
                    </span>
                    <span>Send all</span>
                </button>
            </div>
        </div>
    </div>
    `,
    methods: {
        addFrontend: function() {
            const frontend =
            `
            <div class="field">
                <div class="control">
                    <input name="frontends[]" class="input" type="text">
                </div>
            </div>
            `
            document.getElementById("frontends").innerHTML += frontend
        }
    }
})

var setup_vue = new Vue({
    el: '#main',
    data: {
        stage: "splashscreen",
        dirs: dirs
    }
})
