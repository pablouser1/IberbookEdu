Vue.component('themes', {
    props: ["themes"],
    template:
    `
    <div>
        <div class="columns is-multiline">
            <div v-for="theme in themes" class="column is-narrow">
                <div class="card">
                    <header class="card-header">
                        <p class="card-header-title">{{ theme.name }}</p>
                    </header>
                    <div class="card-content">
                        <div class="content">
                            <p>{{ theme.description }}</p>
                            <p>Is zippable: {{ theme.zip }}</p>
                            <p>Can place banner: {{ theme.banner }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <form action="mngThemes.php?action=add" enctype="multipart/form-data" method="POST">
            <p class="title">Add theme</p>
            <div class="field">
                <div class="control">
                    <input type="file" name="themeZip" accept="application/zip">
                </div>
            </div>
            <button type="submit" class="button is-primary">Upload</button>
        </form>
        <hr>
        <form action="mngThemes.php?action=remove" method="POST">
            <p class="title">Remove theme</p>
            <div class="field">
                <div class="control">
                    <div class="select">
                        <select name="themeName">
                            <option v-for="theme in themes" :value="theme.name">{{ theme.name }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="button is-danger">Remove</button>
        </form>
    </div>
    `,
    data() {
        return {
            themeZip: null
        }
    }
})
