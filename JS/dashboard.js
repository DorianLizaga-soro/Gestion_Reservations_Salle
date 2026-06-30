let dashboardHTML = "";

document.addEventListener("DOMContentLoaded", () => {
document.getElementById("variable").innerHTML="Tableau de bord";
    const links = document.querySelectorAll('.sidebar-link');

  links.forEach(link => {
    link.addEventListener('click', () => {
      links.forEach(l => l.classList.remove('active'));
      link.classList.add('active');
    });
  });


//gestionnaire
 dashboardHTML = document.querySelector(".main-content").innerHTML;

    const btnMenage = document.getElementById("btn-menage");
    if (btnMenage) {
        btnMenage.addEventListener("click", () => {
            fetch("index.php?page=menage")
                .then(r => r.text())
                .then(html => {
                    document.querySelector(".main-content").innerHTML = html;
                    document.getElementById("variable").innerHTML="Gestion du ménage";
                    initMenage();
                });
        });
    }

    // --- BOUTON TABLEAU DE BORD ---
    const btnDashboard = document.getElementById("btn_tableau_de_bord");
    if (btnDashboard) {
        btnDashboard.addEventListener("click", () => {
            document.querySelector(".main-content").innerHTML = dashboardHTML;
            document.getElementById("variable").innerHTML="Tableau de bord";
        });
    }

    // --- BOUTON UTILISATEURS ---
    const btnUtilisateur = document.getElementById("btn_utilisateur");
    if (btnUtilisateur) {
        btnUtilisateur.addEventListener("click", () => {
            fetch("index.php?page=utilisateur")
                .then(r => r.text())
                .then(html => {
                    document.querySelector(".main-content").innerHTML = html;
                    document.getElementById("variable").innerHTML="Utilisateurs";
                    initUtilisateurs();
                });
        });
    }

    // Bouton AssocAdmin
    const btnAssociation = document.getElementById("btn_association");
    if (btnAssociation) {
        btnAssociation.addEventListener("click", () => {
            fetch("index.php?page=associationAdmin")
                .then(r => r.text())
                .then(html => {
                    document.querySelector(".main-content").innerHTML = html;
                    document.getElementById("variable").innerHTML="Associations";
                    initAssocAdmin();
                });
        });
    }

    const btnReservation = document.getElementById("btn_reservation");
    if (btnReservation) {
        btnReservation.addEventListener("click", () => {
            fetch("index.php?page=reservation")
                .then(r => r.text())
                .then(html => {
                    document.querySelector(".main-content").innerHTML = html;
                    document.getElementById("variable").innerHTML="Réservations";
                    initReservation();
                });
        });
    }

    const modal = document.getElementById("modalAddUser");
    const btn = document.getElementById("ajouter_salle");
    const closeBtn = document.querySelector(".close");
    const annulerBtn = document.querySelector(".btn_annuler");

    if (btn) btn.onclick = () => modal.style.display = "flex";
    if (closeBtn) closeBtn.onclick = () => modal.style.display = "none";
    if (annulerBtn) annulerBtn.onclick = () => modal.style.display = "none";

    window.onclick = (e) => {
        if (e.target === modal) modal.style.display = "none";
    };

    const modalEdit = document.getElementById("modalEditUser");
    const closeEdit = document.querySelector(".closeEdit");
    const cancelEdit = document.querySelector(".btn_annulerEdit");

    document.querySelectorAll(".btn_modif").forEach(btn => {
        btn.addEventListener("click", () => {

            const id = btn.dataset.id;
            const nom = btn.dataset.nom;
            const capacite = btn.dataset.capacite;
            const description = btn.dataset.description;
            
            document.getElementById("id_salle").value = id;
            document.getElementById("edit_nom").value = nom;
            document.getElementById("edit_capacite").value = capacite;
            document.getElementById("edit_description").value = description;

            modalEdit.style.display = "flex";
        });
    });

    if (closeEdit) closeEdit.onclick = () => modalEdit.style.display = "none";
    if (cancelEdit) cancelEdit.onclick = () => modalEdit.style.display = "none";

    window.onclick = (e) => {
        if (e.target === modalEdit) modalEdit.style.display = "none";
    };


});
   


document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn_commentaire");
        if (!btn) return;

        const card = btn.closest(".card_menage");
        const zoneCommentaire = card.querySelector(".partie_commentaire");
        zoneCommentaire.classList.toggle("active");
    });

    document.addEventListener("click", function (e) {
    const btn = e.target.closest(".btn_valider");
    if (!btn) return;

    e.preventDefault();

    const form = btn.closest("form");
    const data = new FormData(form);
    data.append('action', 'valider');
    
    fetch("index.php?page=menage", {
        method: "POST",
        body: data
    })
    .then(r => r.text())
    .then(() => {
        // Mise à jour réussie
        // Recharge uniquement la zone ménage dans le dashboard
        fetch("index.php?page=menage")
            .then(r => r.text())
            .then(html => {
                document.querySelector(".main-content").innerHTML = html;
                initMenage(); // réactive les boutons
            });
    });
});









