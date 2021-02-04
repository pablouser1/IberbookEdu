Vue.component('mainmenu', {
    props: ["staff", "schools", "groups", "users"],
    template:
    `
    <div>
        <div>
            <p class="title is-4">
                <i class="fas fa-user-shield"></i>
                <span>Staff</span>
            </p>
            <table class="table is-bordered is-striped is-hoverable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Permissions</th>
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
        <hr>
        <div>
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
            <button type="button" class="button is-info" @click="showSchools = true">
                <span class="icon">
                    <i class="fas fa-school"></i>
                </span>
                <span>Add/remove schools</span>
            </button> 
        </div>
        <hr>
        <div>
            <p class="title is-4">
                <i class="fas fa-school"></i>
                <span>Groups</span>
            </p>
            <table class="table is-bordered is-striped is-hoverable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="group in groups">
                        <td>{{ group.id }}</td>
                        <td>{{ group.name }}</td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="button is-info" @click="showGroups = true">
                <span class="icon">
                    <i class="fas fa-users"></i>
                </span>
                <span>Add/remove groups</span>
            </button>
        </div>
        <hr>
        <div>
            <p class="title is-4">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </p>
            <section class="info-tiles">
                <div class="tile is-ancestor has-text-centered">
                    <div class="tile is-parent">
                        <article class="tile is-child box">
                            <p class="title">{{ users.length }}</p>
                            <p class="subtitle">Users registered</p>
                        </article>
                    </div>
                </div>
            </section>
            <a href="users.php" class="button is-info">Add/remove users</a>
        </div>
        <schools v-bind:schools="schools" v-show="showSchools" @close="showSchools = false"></schools>
        <groups v-bind:groups="groups" v-show="showGroups" @close="showGroups = false"></groups>
    </div>
    `,
    data() {
        return {
            showSchools: false, // Modal schools
            showGroups: false // Modal groups
        }
    }
})
