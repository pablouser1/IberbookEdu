Vue.component('schools', {
    props: ["schools"],
    template: 
    `
    <div>
        <div class="modal is-active">
            <button class="modal-close" @click="$emit('close')"></button> 
            <div class="modal-background" @click="$emit('close')"></div>
            <div class="modal-content">
                <div class="box">
                    <form action="mngSchools.php?action=add" method="post">
                        <p class="title">Add</p>
                        <div class="field">
                            <label class="label">School id</label>
                            <div class="control">
                                <input name="schoolid" class="input" type="text"/>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">School name</label>
                            <div class="control">
                                <input name="schoolname" class="input" type="text"/>
                            </div>
                        </div>
                        <button class="button is-success" type="submit">Add</button>
                    </form>
                    <hr>
                    <form action="mngSchools.php?action=remove" method="post">
                        <p class="title">Remove</p>
                        <div class="field">
                            <div class="control">
                                <div class="select">
                                    <select name="schoolid">
                                        <option v-for="school in schools" :value="school.id">{{ school.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="button is-danger" type="submit">Remove</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    `
});

Vue.component('groups', {
    props: ["groups"],
    template: 
    `
    <div>
        <div class="modal is-active">
            <button class="modal-close" @click="$emit('close')"></button> 
            <div class="modal-background" @click="$emit('close')"></div>
            <div class="modal-content">
                <div class="box">
                    <form action="mngGroups.php?action=add" method="post">
                        <p class="title">Add</p>
                        <div class="field">
                            <label class="label">Group name</label>
                            <div class="control">
                                <input name="groupname" class="input" type="text"/>
                            </div>
                        </div>
                        <button class="button is-success" type="submit">Add</button>
                    </form>
                    <hr>
                    <form action="mngGroups.php?action=remove" method="post">
                        <p class="title">Remove</p>
                        <div class="field">
                            <div class="control">
                                <div class="select">
                                    <select name="groupid">
                                        <option v-for="group in groups" :value="group.id">{{ group.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button class="button is-danger" type="submit">Remove</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    `
});
