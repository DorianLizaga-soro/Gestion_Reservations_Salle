function initMenage() {

    // Bouton commentaire
    

    // Gestion caractères
    const input = document.getElementById('input_commentaire');
    const info = document.getElementById('info');
    const max = 50;

    if (input && info) {
        input.addEventListener('input', () => {
            if (input.value.length > max) {
                input.value = input.value.slice(0, max);
            }
            info.textContent = `${input.value.length} / ${max} caractères`;
            info.className = input.value.length === max ? 'info warning' : 'info';
        });
    }

    // Filtres
    const btnTous = document.getElementById("btn_tous");
    const btnFaire = document.getElementById("btn_faire");
    const btnAttente = document.getElementById("btn_attente");

    const cartes = document.querySelectorAll(".card_menage");

    function filtrer(statuts) {
        cartes.forEach(carte => {
            const statut = carte.getAttribute("data-statut");
            carte.style.display = statuts.includes(statut) ? "block" : "none";
        });
    }

    if (btnTous) btnTous.addEventListener("click", () => filtrer(["a_faire", "attente"]));
    if (btnFaire) btnFaire.addEventListener("click", () => filtrer(["a_faire"]));
    if (btnAttente) btnAttente.addEventListener("click", () => filtrer(["attente"]));

    // ⭐⭐ ENVOI DU COMMENTAIRE
    document.querySelectorAll(".partie_commentaire form").forEach(form => {
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const data = new FormData(form);

            fetch("index.php?page=menage", {
                method: "POST",
                body: data
            })
            .then(r => r.text())
            .then(() => {

                // Recharge la page ménage
                fetch("index.php?page=menage")
                    .then(r => r.text())
                    .then(html => {
                        document.querySelector(".main-content").innerHTML = html;

                        // ⭐ IMPORTANT : réinitialiser les events
                        initMenage();
                    });
            });
        });
    });
}
