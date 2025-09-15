function toggleMenu(event, menuId) {
    event.preventDefault();
    const menu = document.getElementById(menuId);
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}


document.addEventListener("DOMContentLoaded", () => {
    const fechaInput = document.getElementById("Fecha");
    if (fechaInput) {
        const hoy = new Date().toISOString().split("T")[0];
        fechaInput.setAttribute("min", hoy);
    }
});