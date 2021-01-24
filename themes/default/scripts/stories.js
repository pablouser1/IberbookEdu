// Users' stories
var stories = {
    props: ["user"],
    methods: {
        stopVideo: function() {
            document.getElementById("videoStories").pause()
            this.$emit('close')
        }
    },
    mounted() {
        document.getElementById("videoStories").load()
        document.getElementById("videoStories").play()
    },
    template:
    `
    <div>
        <div class="modal is-active">
            <div class="modal-background" @click="stopVideo"></div>
            <div class="modal-content fade-in">
                <div class="container has-text-centered">
                    <video id="videoStories" v-on:ended="stopVideo" controls>
                        <source :src="'users/' + user.id + '/' + user.video">
                    </video>
                </div>
            </div>
            <button class="modal-close" @click="stopVideo"></button> 
        </div>
    </div>
    `
}
