const modal = document.getElementById("modalAddUser");
const btn = document.getElementById("btnAddUser");
const closeBtn = document.querySelector(".close");
const annulerBtn = document.querySelector(".btn_annuler");

btn.onclick = () => {
    modal.style.display = "flex";
}

closeBtn.onclick = () => {
    modal.style.display = "none";
}

annulerBtn.onclick = () => {
    modal.style.display = "none";
}

window.onclick = (e) => {
    if (e.target === modal) {
        modal.style.display = "none";
    }
}

//Bouton Rôle 

const roleButtons = document.querySelectorAll(".role-btn");

roleButtons.forEach(btn => {
    btn.addEventListener("click", () => {
        roleButtons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
    });
});

