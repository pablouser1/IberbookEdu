// Users' stories
var stories = {
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    methods: {
        stopVideo () {
            document.getElementById("videoStories").pause()
            this.$emit('close')
        }
    },
    mounted () {
        document.getElementById("videoStories").load()
        document.getElementById("videoStories").play()
    },
    template:
    `
    <div class="modal is-active">
        <div class="modal-background" @click="stopVideo"></div>
        <div id="videoModal" class="modal-content fade-in has-text-centered" style="overflow: hidden;">
            <video id="videoStories" @ended="stopVideo" controls>
                <source :src="'users/' + user.id + '/' + user.video">
            </video>
        </div>
        <button class="modal-close" @click="stopVideo"></button>
    </div>
    `
}
