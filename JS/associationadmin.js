/* ========================================
   OUVERTURE DE LA MODALE "VOIR DÉTAILS"
   ======================================== */

document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".btn-voir-details").forEach(function (bouton) {
        bouton.addEventListener("click", function () {
console.log("ok");
            const id = bouton.dataset.id; // ID de l'association
            const overlay = document.getElementById("overlay-details-" + id);

            if (overlay) {
                overlay.classList.add("actif");
            }
        });
    });


    /* ========================================
       FERMETURE DE LA MODALE
       ======================================== */
    document.querySelectorAll(".fermer-details").forEach(function (bouton) {
        bouton.addEventListener("click", function () {

            const id = bouton.dataset.id;
            const overlay = document.getElementById("overlay-details-" + id);

            if (overlay) {
                overlay.classList.remove("actif");
            }
        });
    });


    /* Fermeture en cliquant sur le fond */
    document.querySelectorAll(".overlay-details").forEach(function (overlay) {
        overlay.addEventListener("click", function (event) {
            if (event.target === overlay) {
                overlay.classList.remove("actif");
            }
        });
    });
// Gestion des onglets dans chaque modale
    document.querySelectorAll(".modale-details").forEach(modale => {

        const onglets = modale.querySelectorAll(".onglet-btn");
        const panels = modale.querySelectorAll(".onglet-panel");

        onglets.forEach(btn => {
            btn.addEventListener("click", () => {

                const cible = btn.dataset.onglet; // info, cal, histo, docs

                // Désactiver tous les onglets
                onglets.forEach(o => o.classList.remove("actif"));

                // Activer celui cliqué
                btn.classList.add("actif");

                // Masquer tous les panels
                panels.forEach(p => p.classList.remove("actif"));

                // Afficher le bon panel
                modale.querySelector("#panel-" + cible).classList.add("actif");
            });
        });

    });


/* ========= AJOUT ASSOCIATION ========= */
    const ouvrirAjoutAsso = document.getElementById("ouvrir-ajout-association");
    const overlayAjoutAsso = document.getElementById("overlay-ajout-asso");
    const fermerAjoutAsso = document.getElementById("fermer-ajout-asso");
    const annulerAjoutAsso = document.getElementById("annuler-ajout-asso");

    if (ouvrirAjoutAsso && overlayAjoutAsso) {
        ouvrirAjoutAsso.addEventListener("click", function () {
            overlayAjoutAsso.classList.add("actif");
        });
        fermerAjoutAsso.addEventListener("click", function () {
            overlayAjoutAsso.classList.remove("actif");
        });
        annulerAjoutAsso.addEventListener("click", function () {
            overlayAjoutAsso.classList.remove("actif");
        });
        overlayAjoutAsso.addEventListener("click", function (e) {
            if (e.target === overlayAjoutAsso) {
                overlayAjoutAsso.classList.remove("actif");
            }
        });
    }

    /* ========= MODIFIER ASSOCIATION ========= */
    const overlayModifierAsso = document.getElementById("overlay-modifier-asso");
    const fermerModifierAsso = document.getElementById("fermer-modifier-asso");
    const annulerModifierAsso = document.getElementById("annuler-modifier-asso");
    const modifierNom = document.getElementById("modifier-nom");
    const modifierCouleur = document.getElementById("modifier-couleur");
    const modifierMembre = document.getElementById("modifier-membre");

    document.querySelectorAll(".btn-modifier-asso").forEach(function (bouton) {
    bouton.addEventListener("click", function () {
        modifierNom.value = bouton.dataset.nom;
        modifierCouleur.value = bouton.dataset.couleur;
        document.getElementById("modifier-id-asso").value = bouton.dataset.id;
        document.getElementById("modifier-membre").value = bouton.dataset.responsable; // ← cette ligne est là ?
        overlayModifierAsso.classList.add("actif");
    });
});

    fermerModifierAsso.addEventListener("click", function () {
        overlayModifierAsso.classList.remove("actif");
    });
    annulerModifierAsso.addEventListener("click", function () {
        overlayModifierAsso.classList.remove("actif");
    });
    overlayModifierAsso.addEventListener("click", function (e) {
        if (e.target === overlayModifierAsso) {
            overlayModifierAsso.classList.remove("actif");
        }
    });

    /* ========= SUPPRESSION ASSOCIATION ========= */
    const overlaySupprimerAsso = document.getElementById("overlay-supprimer-asso");
    const fermerSupprimerAsso = document.getElementById("fermer-supprimer-asso");
    const annulerSupprimerAsso = document.getElementById("annuler-supprimer-asso");
    const texteSuppressionAsso = document.getElementById("texte-suppression-asso");

    document.querySelectorAll(".btn-supprimer-asso").forEach(function (bouton) {
        bouton.addEventListener("click", function () {
            const nom = bouton.dataset.nom;
            const id = bouton.dataset.id;

            document.getElementById("id-asso-supprimer").value = id;

            texteSuppressionAsso.textContent = "Êtes-vous sûr de vouloir supprimer l'association \"" + nom + "\" ?";
            overlaySupprimerAsso.classList.add("actif");
        });
    });

    fermerSupprimerAsso.addEventListener("click", function () {
        overlaySupprimerAsso.classList.remove("actif");
    });
    annulerSupprimerAsso.addEventListener("click", function () {
        overlaySupprimerAsso.classList.remove("actif");
    });
    overlaySupprimerAsso.addEventListener("click", function (e) {
        if (e.target === overlaySupprimerAsso) {
            overlaySupprimerAsso.classList.remove("actif");
        }
    });

});


    



