// Burger menu (mobile/tablet only)
var burger = document.getElementById("navbar-burger")
burger.addEventListener("click", () => {
    const target = burger.dataset.target
    const $target = document.getElementById(target)
    burger.classList.toggle("is-active")
    $target.classList.toggle("is-active")
})

// Initial pic
const pic = document.getElementById("banner_image")
const caption = document.getElementById("banner_caption")

fetch("getbanner.php")
// Get json response
.then(res => {
    return res.json()
})
.then(json_res => {
    if (json_res.code == "C") {
        caption.innerHTML = `${json_res.data.schoolyear} - ${json_res.data.acyear} / <a href="yearbooks.php?id=${json_res.data.id}">Visitar</a>`
        pic.src = json_res.data.url
    }
    else {
        pic.src = ""
    }
})
