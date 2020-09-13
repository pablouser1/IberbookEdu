Vue.component("database", {
    template:
    `
    <div class="section">
        <h1 class="title">Base de datos</h1>
        <hr>
        <div class="field">
            <label class="label">Nombre de la base de datos</label>
            <div class="control">
                <input name="db[]" id="name" class="input" type="text" placeholder="Ej: iberbook_db" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Host</label>
            <div class="control">
                <input name="db[]" id="host" class="input" type="text" placeholder="Ej: localhost" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Puerto</label>
            <div class="control">
                <input name="db[]" id="port" class="input" type="text" value="3306" required>
            </div>
            <p class="help">Por detecto es 3306</p>
        </div>
        <div class="field">
            <label class="label">Nombre de usuario</label>
            <div class="control">
                <input name="db[]" id="username" class="input" type="text" placeholder="Ej: usuario" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Contraseña</label>
            <div class="control">
                <input name="db[]" id="password" class="input" type="password" placeholder="**********" required>
            </div>
        </div>
        <div class="field">
            <div class="control">
                <button v-on:click="$root.stage = 'owner'" type="button" class="button is-success">
                    <span class="icon">
                        <i class="fas fa-forward"></i>
                    </span>
                    <span>Siguiente</span>
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
        <h1 class="title">Datos del servidor</h1>
        <h2 class="title">Cuenta del dueño</h2>
        <h2 class="subtitle">Esta cuenta tendrá los máximos permisos posibles</h2>
        <div class="field">
            <label class="label">Nombre de usuario</label>
            <div class="control">
                <input name="owner[]" id="username" class="input" type="text" placeholder="Ej: usuario" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Contraseña</label>
            <div class="control">
                <input name="owner[]" id="password" class="input" type="password" placeholder="***********" required>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button v-on:click="$root.stage = 'database'" type="button" class="button is-info">
                    <span class="icon">
                        <i class="fas fa-backward"></i>
                    </span>
                    <span>Atrás</span>
                </button>
            </div>
            <div class="control">
                <button v-on:click="$root.stage = 'server'" type="button" class="button is-success">
                    <span class="icon">
                        <i class="fas fa-forward"></i>
                    </span>
                    <span>Siguiente</span>
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
    template:
    `
    <div class="section">
        <h1 class="title">Datos del servidor</h1>
        <h2 class="title">Opciones</h2>
        <div class="field">
            <label class="label">Selecciona tu comunidad autónoma</label>
            <div class="control">
                <div class="select">
                    <select name="global[]" id="comunidadaut">
                        <option value="andalucia">Andalucía</option>
                        <option value="madrid">Madrid</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="field">
            <label class="label">Ubicación del directorio de subida</label>
            <div class="control">
                <input name="global[]" class="input" type="text" :value="dirs.upload" required>
            </div>
            <p class="help">Aquí se guardan los archivos subidos por los usuarios, MUY recomendable que sea un directorio privado</p>
        </div>
        <div class="field">
            <label class="label">Ubicación de los yearbooks</label>
            <div class="control">
                <input name="global[]" class="input" type="text" :value="dirs.yearbook" required>
            </div>
            <p class="help">Aquí se guardan los yearbooks ya generados, MUY recomendable que sea un directorio público</p>
        </div>
        <div class="field">
            <h2 class="title">Centro admitido</h2>
            <label class="label">ID del centro</label>
            <div class="control">
                <input name="schoolinfo[]" class="input" type="number" placeholder="Ej: 181206713" required>
            </div>
            <p class="help">Puedes conseguir la información necesaria <a href="https://www.juntadeandalucia.es/educacion/vscripts/centros/index.asp" target="_blank">aquí</a> (Andalucia) o <a href="https://www.madrid.org/wpad_pub/run/j/MostrarConsultaGeneral.icm" target="_blank">aquí</a> (Madrid)</p>
            <label class="label">URL (opcional)<i class="fas fa-link"></i></label>
            <div class="control">
                <input name="schoolinfo[]" class="input" type="text">
                <p class="help">Esta URL saldrá en cada orla generada</p>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button v-on:click="$root.stage = 'owner'" type="button" class="button is-info">
                    <span class="icon">
                        <i class="fas fa-backward"></i>
                    </span>
                    <span>Atrás</span>
                </button>
            </div>
            <div class="control">
                <button type="submit" class="button is-success">
                    <span class="icon">
                        <i class="fas fa-paper-plane"></i>
                    </span>
                    <span>Enviar todo</span>
                </button>
            </div>
        </div>
    </div>
    `
})

var setup_vue = new Vue({
    el: '#main',
    data: {
        stage: "splashscreen",
        dirs: dirs
    }
})
