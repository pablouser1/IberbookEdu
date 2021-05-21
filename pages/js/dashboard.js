// STAFF //

// Add
const staff_add_button = document.getElementById("staff_add_button")
const staff_add_modal = new BulmaModal("#staff_add_modal")
const staff_add_form = document.getElementById("staff_add_form")

staff_add_button.addEventListener("click", () => {
    staff_add_modal.show()
})
staff_add_form.addEventListener('submit', async (e) => {
    e.preventDefault()
    const form = new FormData(e.target)
    const res = await requests('../../staff', 'POST', form)
    alert(res)
})

// Remove
const staff_remove_button = document.getElementById("staff_remove_button")
const staff_remove_modal = new BulmaModal("#staff_remove_modal")
const staff_remove_form = document.getElementById("staff_remove_form")

staff_remove_button.addEventListener("click", () => {
    staff_remove_modal.show()
})
staff_remove_form.addEventListener('submit', async (e) => {
    e.preventDefault()
    const form = new FormData(e.target)
    const res = await requests('../../staff', 'DELETE', form)
    alert(res)
})

// SCHOOLS //

// Add
const schools_add_button = document.getElementById("schools_add_button")
const schools_add_modal = new BulmaModal("#schools_add_modal")
const schools_add_form = document.getElementById("schools_add_form")

schools_add_button.addEventListener("click", () => {
    schools_add_modal.show()
})
schools_add_form.addEventListener('submit', async (e) => {
    e.preventDefault()
    const form = new FormData(e.target)
    const res = await requests('../../schools', 'POST', form)
    alert(res)
})

// Remove
const schools_remove_button = document.getElementById("schools_remove_button")
const schools_remove_modal = new BulmaModal("#schools_remove_modal")
const schools_remove_form = document.getElementById("schools_remove_form")

schools_remove_button.addEventListener("click", () => {
    schools_remove_modal.show()
})
schools_remove_form.addEventListener('submit', async (e) => {
    e.preventDefault()
    const form = new FormData(e.target)
    const res = await requests('../../schools', 'DELETE', form)
    alert(res)
})

// GROUPS //

// Add
const groups_add_button = document.getElementById("groups_add_button")
const groups_add_modal = new BulmaModal("#groups_add_modal")
const groups_add_form = document.getElementById("groups_add_form")

groups_add_button.addEventListener("click", () => {
    groups_add_modal.show()
})
groups_add_form.addEventListener('submit', async (e) => {
    e.preventDefault()
    const form = new FormData(e.target)
    const res = await requests('../../groups', 'POST', form)
    alert(res)
})

// Remove
const groups_remove_button = document.getElementById("groups_remove_button")
const groups_remove_modal = new BulmaModal("#groups_remove_modal")
const groups_remove_form = document.getElementById("groups_remove_form")

groups_remove_button.addEventListener("click", () => {
    groups_remove_modal.show()
})
groups_remove_form.addEventListener('submit', async (e) => {
    e.preventDefault()
    const form = new FormData(e.target)
    const res = await requests('../../groups', 'DELETE', form)
    alert(res)
})
