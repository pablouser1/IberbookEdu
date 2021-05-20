// Gallery
var gallery = {
    props: {
        "gallery": {
            type: Array,
            required: true
        }
    },
    template:
    `
    <div class="columns is-centered is-multiline is-vcentered fade-in">
        <div v-for="(item) in gallery" class='column is-one-third-tablet is-one-third-desktop is-one-quarter-widescreen is-one-fifth-fullhd'>
            <article class="media">
                <div class="media-content">
                    <figure v-if="item.type == 'photo'" class="image">
                        <img :src="dataUrl + 'gallery/' + item.name">
                    </figure>
                    <video v-else preload="metadata" controls>
                        <source :src="dataUrl + 'gallery/' + item.name"></source>
                    </video>
                </div>
            </article>
        </div>
    </div>
    `
}
