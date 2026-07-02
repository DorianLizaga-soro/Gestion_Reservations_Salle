
function initUtilisateurs() {

    // MODAL 
    const modal = document.getElementById("modalAddUser");
    const btn = document.getElementById("btnAddUser");
    const closeBtn = document.querySelector(".close");
    const annulerBtn = document.querySelector(".btn_annuler");

    if (btn) btn.onclick = () => modal.style.display = "flex";
    if (closeBtn) closeBtn.onclick = () => modal.style.display = "none";
    if (annulerBtn) annulerBtn.onclick = () => modal.style.display = "none";

    window.onclick = (e) => {
        if (e.target === modal) modal.style.display = "none";
    };

    // CONFIRMATION SUPPRESSION 
    document.querySelectorAll(".form_delete_user").forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            if (confirm("Voulez-vous vraiment supprimer cet utilisateur ?")) {
                form.submit();
            }
        });
    });

    // DISPARITION AUTOMATIQUE DES ALERTES 
    const alertBox = document.querySelector(".alert");

    if (alertBox) {
        setTimeout(() => {
            alertBox.style.opacity = "0";
            alertBox.style.transition = "opacity 0.5s ease";

            setTimeout(() => {
                alertBox.remove();
                window.location.href = "/index.php?page=utilisateurs";
            }, 500);

        }, 3000);
    }

    // --- RECHERCHE + FILTRE ROLE ---
    const searchInput = document.getElementById("searchUser");
    const selectRole = document.getElementById("selectUser");
    const users = document.querySelectorAll(".div_userElement");

    function filterUsers() {
        const searchValue = searchInput.value.toLowerCase();
        const roleValue = selectRole.value;

        users.forEach(user => {
            const name = user.querySelector(".div_user p").textContent.toLowerCase();
            const email = user.querySelector("p:nth-child(2)").textContent.toLowerCase();
            const role = user.querySelector("p:nth-child(4)").textContent.toLowerCase();

            const matchSearch =
                name.includes(searchValue) ||
                email.includes(searchValue);

            const matchRole =
                roleValue === "toutRole" ||
                role.includes(roleValue);

            user.style.display = (matchSearch && matchRole) ? "flex" : "none";
        });
    }

    if (searchInput) searchInput.addEventListener("input", filterUsers);
    if (selectRole) selectRole.addEventListener("change", filterUsers);

    // --- MODAL EDIT ---
    const modalEdit = document.getElementById("modalEditUser");
    const closeEdit = document.querySelector(".closeEdit");
    const cancelEdit = document.querySelector(".btn_annulerEdit");

    document.querySelectorAll(".btn_modif").forEach(btn => {
        btn.addEventListener("click", () => {

            const id = btn.dataset.id;
            const nom = btn.dataset.nom;
            const email = btn.dataset.email;
            const role = btn.dataset.role;
            const association = btn.dataset.association;

            document.getElementById("edit_id").value = id;
            document.getElementById("edit_nom").value = nom;
            document.getElementById("edit_email").value = email;
            document.getElementById("edit_role").value = role;
            document.getElementById("edit_association").value = association;

            modalEdit.style.display = "flex";
        });
    });

    if (closeEdit) closeEdit.onclick = () => modalEdit.style.display = "none";
    if (cancelEdit) cancelEdit.onclick = () => modalEdit.style.display = "none";

    window.onclick = (e) => {
        if (e.target === modalEdit) modalEdit.style.display = "none";
    };
}

