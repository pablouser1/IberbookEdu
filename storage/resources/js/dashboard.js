// SCHOOLS //
const schools_button = document.getElementById("schools_button")
const schools_modal = new BulmaModal("#schools_modal")
schools_button.addEventListener("click", () => {
    schools_modal.show()
})

// GROUPS //
const groups_button = document.getElementById("groups_button")
const groups_modal = new BulmaModal("#groups_modal")
groups_button.addEventListener("click", () => {
    groups_modal.show()
})

// STAFF //
const staff_button = document.getElementById("staff_button")
const staff_modal = new BulmaModal("#staff_modal")
staff_button.addEventListener("click", () => {
    staff_modal.show()
})
