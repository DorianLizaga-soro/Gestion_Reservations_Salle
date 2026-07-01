let dashboardHTML = "";

document.addEventListener("DOMContentLoaded", () => {
    updateNotifications(); 
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

    // --- BOUTON Messagerie ---
    const btnMessagerie = document.getElementById("btn_messagerie");
    if (btnMessagerie) {
        btnMessagerie.addEventListener("click", () => {
            fetch("index.php?page=messagerie")
                .then(r => r.text())
                .then(html => {
                    updateNotifications();
                    document.querySelector(".main-content").innerHTML = html;
                    document.getElementById("variable").innerHTML="Messagerie";
                    initMessagerie();
                });
        });
    }

    
    // --- BOUTON Exportations ---
// --- BOUTON Exportations ---
const btnExportations = document.getElementById("exportation");
if (btnExportations) {
    btnExportations.addEventListener("click", () => {

        fetch("index.php?page=exportations")
            .then(r => r.text())
            .then(html => {

                document.querySelector(".main-content").innerHTML = html;
                document.getElementById("variable").innerHTML = "Exportations";

                // 🔥 FORMULAIRE EXCEL
                const formExcel = document.querySelector("form[action='./index.php?page=export_excel_admin']");
                if (formExcel) {
                    formExcel.addEventListener("submit", async (e) => {
                        e.preventDefault();

                        const formData = new FormData(formExcel);

                        const response = await fetch("./index.php?page=export_excel_admin_ajax", {
                            method: "POST",
                            body: formData
                        });

                        const data = await response.json();

                        const ligne = await fetch("./index.php?page=exportations_ajax", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: "id=" + data.id + "&type=excel"
                        });

                        // 2) Ajouter la ligne dans l’historique
                        const htmlLigne = await ligne.text();

                        // 🔥 Supprimer "Aucun export disponible" si présent
                        const emptyLine = document.querySelector("#zone_historique .div_ligne p[colspan]");
                        if (emptyLine) {
                            emptyLine.parentElement.remove();
                        }

                        document.querySelector("#zone_historique").insertAdjacentHTML("afterbegin", htmlLigne);


                        window.location.href = data.chemin;
                    });
                }

                // 🔥 FORMULAIRE CALENDAR
                const formCalendar = document.querySelector("form[action='./index.php?page=export_calendar_admin']");
                if (formCalendar) {
                    formCalendar.addEventListener("submit", async (e) => {
                        e.preventDefault();

                        const formData = new FormData(formCalendar);

                        const response = await fetch("./index.php?page=export_calendar_admin_ajax", {
                            method: "POST",
                            body: formData
                        });

                        const data = await response.json();

                        const ligne = await fetch("./index.php?page=exportations_ajax", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: "id=" + data.id + "&type=calendar"
                        });

                        // 2) Ajouter la ligne dans l’historique
                        const htmlLigne = await ligne.text();

                        // 🔥 Supprimer "Aucun export disponible" si présent
                        const emptyLine = document.querySelector("#zone_historique .div_ligne p[colspan]");
                        if (emptyLine) {
                            emptyLine.parentElement.remove();
                        }

                        document.querySelector("#zone_historique").insertAdjacentHTML("afterbegin", htmlLigne);


                        window.location.href = data.chemin;
                    });
                }

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
                    updateNotifications();
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


    const bell = document.getElementById("notif-bell");
const panel = document.getElementById("notif-panel");
const badge = document.getElementById("notif-badge");
const content = document.getElementById("notif-content");

bell.addEventListener("click", () => {
    panel.classList.toggle("hidden");
    updateNotifications(); // 🔥 met à jour à chaque ouverture
});


function updateNotifications() {
    fetch("./index.php?page=notifications_ajax")
        .then(res => res.json())
        .then(data => {

            // Mettre à jour le badge
            badge.textContent = data.total;
            badge.style.display = data.total > 0 ? "inline-block" : "none";

            // Si le panneau est ouvert, on met aussi à jour son contenu
            if (!panel.classList.contains("hidden")) {
                content.innerHTML = `
                    <div class="notif-item">
                        <strong>${data.reservations}</strong> réservations en attente
                    </div>
                    <div class="notif-item">
                        <strong>${data.messages}</strong> nouveaux messages
                    </div>
                `;
            }
        });
}


  

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









