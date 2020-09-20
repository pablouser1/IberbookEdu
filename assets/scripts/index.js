// Burger menu (mobile/tablet only)
var burger = document.getElementById("navbar-burger")
burger.addEventListener("click", () => {
    const target = burger.dataset.target
    const $target = document.getElementById(target)
    burger.classList.toggle("is-active")
    $target.classList.toggle("is-active")
})
