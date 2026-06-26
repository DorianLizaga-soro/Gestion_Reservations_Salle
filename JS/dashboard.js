let dashboardHTML = "";

document.addEventListener("DOMContentLoaded", () => {

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
                    initMenage();
                });
        });
    }

    // --- BOUTON TABLEAU DE BORD ---
    const btnDashboard = document.getElementById("btn_tableau_de_bord");
    if (btnDashboard) {
        btnDashboard.addEventListener("click", () => {
            document.querySelector(".main-content").innerHTML = dashboardHTML;
            initDashboard();
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
                    initUtilisateurs();
                });
        });
    }

});
   

//menage

/*
document.getElementById("btn-menage").addEventListener("click", () => {
    fetch("index.php?page=menage")
        .then(response => response.text())
        .then(html => {
            document.querySelector(".main-content").innerHTML = html;

            
                initMenage(); // <-- APPEL APRÈS CHARGEMENT
            
        });
});
*/
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









