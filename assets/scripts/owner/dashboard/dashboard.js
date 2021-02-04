// Root instance
var dashboard_vue = new Vue({
    el: '#main',
    data: {
        tab: "mainmenu",
        staff: staff_js,
        schools: schools_js,
        groups: groups_js,
        themes: themes_js,
        users: users_js
    },
    methods: {
        changeTab: function(tab) {
            this.tab = tab
            document.getElementById("items").scrollIntoView({
                behavior: 'smooth'
            })
        }
    }
})
