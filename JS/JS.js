

/* Page association JS*/




/* association boutton deroulant pour voir les reservations*/

const boutons = document.querySelectorAll(".entete-bouton");

boutons.forEach(function (bouton) {
    bouton.addEventListener("click", function () {
        const idPanneau = bouton.getAttribute("data-cible");
        const panneau = document.getElementById(idPanneau);
        const chevron = bouton.querySelector(".chevron");

        const estOuvert = panneau.style.maxHeight && panneau.style.maxHeight !== "0px";

        if (estOuvert) {
            panneau.style.maxHeight = "0";
            chevron.classList.remove("ouvert");
        } else {
            panneau.style.maxHeight = panneau.scrollHeight + "px";
            chevron.classList.add("ouvert");
        }
    });
});





const boutonOuvrir = document.getElementById("ouvrir-modale");
const overlay = document.getElementById("overlay-reservation");
const boutonFermer = document.getElementById("fermer-modale");
const boutonAnnuler = document.getElementById("annuler-modale");

if (boutonOuvrir && overlay) {
    boutonOuvrir.addEventListener("click", function () {
        overlay.classList.add("actif");
    });
}

if (boutonFermer && overlay) {
    boutonFermer.addEventListener("click", function () {
        overlay.classList.remove("actif");
    });
}

if (boutonAnnuler && overlay) {
    boutonAnnuler.addEventListener("click", function () {
        overlay.classList.remove("actif");
    });
}

if (overlay) {
    overlay.addEventListener("click", function (event) {
        if (event.target === overlay) {
            overlay.classList.remove("actif");
        }
    });
}





/* ========== SUPPRESSION ========== */

const overlaySuppression = document.getElementById("overlay-suppression");
const fermerSuppression = document.getElementById("fermer-modale-suppression");
const annulerSuppression = document.getElementById("annuler-suppression");
const inputIdASupprimer = document.getElementById("id-reservation-a-supprimer");






// Pour chaque bouton "Annuler" de réservation (qui sert de bouton supprimer),
// on ouvre la modale de confirmation au lieu de supprimer directement
if (overlaySuppression && inputIdASupprimer) {

    document.querySelectorAll(".btn-annuler").forEach(function (bouton) {
        bouton.addEventListener("click", function () {
            const idReservation = bouton.getAttribute("data-id-reservation");

            inputIdASupprimer.value = idReservation;
            overlaySuppression.classList.add("actif");
        });
    });

    if (fermerSuppression) {
        fermerSuppression.addEventListener("click", function () {
            overlaySuppression.classList.remove("actif");
        });
    }

    if (annulerSuppression) {
        annulerSuppression.addEventListener("click", function () {
            overlaySuppression.classList.remove("actif");
        });
    }

    overlaySuppression.addEventListener("click", function (event) {
        if (event.target === overlaySuppression) {
            overlaySuppression.classList.remove("actif");
        }
    });

}


/* ========== MODIFICATION ========== */

const overlayModification = document.getElementById("overlay-modification");
const fermerModification = document.getElementById("fermer-modale-modification");
const annulerModification = document.getElementById("annuler-modification");
const inputIdAModifier = document.getElementById("id-reservation-a-modifier");
const modifSalle = document.getElementById("modif-salle");
const modifDate = document.getElementById("modif-date");
const modifHeureDebut = document.getElementById("modif-heure-debut");
const modifHeureFin = document.getElementById("modif-heure-fin");

if (overlayModification && inputIdAModifier) {

    document.querySelectorAll(".btn-modifier").forEach(function (bouton) {
        bouton.addEventListener("click", function () {

            const idReservation = bouton.getAttribute("data-id-reservation");
            const idSalle = bouton.getAttribute("data-id-salle");
            const date = bouton.getAttribute("data-date");
            const heureDebut = bouton.getAttribute("data-heure-debut");
            const heureFin = bouton.getAttribute("data-heure-fin");

            inputIdAModifier.value = idReservation;

            if (idSalle) modifSalle.value = idSalle;
            if (date) modifDate.value = date;
            if (heureDebut) modifHeureDebut.value = heureDebut;
            if (heureFin) modifHeureFin.value = heureFin;

            overlayModification.classList.add("actif");
        });
    });

    if (fermerModification) {
        fermerModification.addEventListener("click", function () {
            overlayModification.classList.remove("actif");
        });
    }

    if (annulerModification) {
        annulerModification.addEventListener("click", function () {
            overlayModification.classList.remove("actif");
        });
    }

    overlayModification.addEventListener("click", function (event) {
        if (event.target === overlayModification) {
            overlayModification.classList.remove("actif");
        }
    });

}





/* ========== UPLOAD DE FICHIER ========== */

// Quand l'utilisateur choisit un fichier, on affiche son nom et on envoie le formulaire
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('input[type="file"]').forEach(function (inputFichier) {
        inputFichier.addEventListener("change", function () {
            if (inputFichier.files.length > 0) {
                const nomFichier = inputFichier.files[0].name;
                const spanNom = document.getElementById("nom-" + inputFichier.id);

                if (spanNom) {
                    spanNom.textContent = nomFichier;
                }

                inputFichier.closest("form").submit();
            }
        });
    });

});







/* ========== modal de deconnexion ========== */




const boutonOuvrirDeconnexion = document.getElementById("ouvrir-modale-deconnexion");
const overlayDeconnexion = document.getElementById("overlay-deconnexion");
const fermerDeconnexion = document.getElementById("fermer-modale-deconnexion");
const annulerDeconnexion = document.getElementById("annuler-deconnexion");

if (boutonOuvrirDeconnexion && overlayDeconnexion) {
    boutonOuvrirDeconnexion.addEventListener("click", function () {
        overlayDeconnexion.classList.add("actif");
    });
}

if (fermerDeconnexion && overlayDeconnexion) {
    fermerDeconnexion.addEventListener("click", function () {
        overlayDeconnexion.classList.remove("actif");
    });
}

if (annulerDeconnexion && overlayDeconnexion) {
    annulerDeconnexion.addEventListener("click", function () {
        overlayDeconnexion.classList.remove("actif");
    });
}

if (overlayDeconnexion) {
    overlayDeconnexion.addEventListener("click", function (event) {
        if (event.target === overlayDeconnexion) {
            overlayDeconnexion.classList.remove("actif");
        }
    });
}

function deconnexion() {

    window.location.href = '/HTML/connexion.html';

}


/*modal d export de fichier*/



const ouvrirExport = document.getElementById("ouvrir-export");
const overlayExport = document.getElementById("overlay-export");
const fermerExport = document.getElementById("fermer-export");

if (ouvrirExport && overlayExport) {
    ouvrirExport.addEventListener("click", () => {
        overlayExport.classList.add("actif");
    });
}

if (fermerExport && overlayExport) {
    fermerExport.addEventListener("click", () => {
        overlayExport.classList.remove("actif");
    });
}

if (overlayExport) {
    overlayExport.addEventListener("click", (e) => {
        if (e.target === overlayExport) {
            overlayExport.classList.remove("actif");
        }
    });
}



/*ouverture modal d ajout*/


document.addEventListener("DOMContentLoaded", function () {

    const btnOpen = document.getElementById("ouvrir-ajout-membre");
    const modal = document.getElementById("overlay-ajout-membre");
    const btnClose = document.getElementById("fermer-ajout-membre");
    const btnCancel = document.getElementById("annuler-ajout-membre");

    if (btnOpen && modal) {
        btnOpen.addEventListener("click", function () {
            modal.classList.add("actif");
        });
    }

    if (btnClose && modal) {
        btnClose.addEventListener("click", function () {
            modal.classList.remove("actif");
        });
    }

    if (btnCancel && modal) {
        btnCancel.addEventListener("click", function () {
            modal.classList.remove("actif");
        });
    }

});



/* FIN de page association JS*/



/*-------------------------------------------------------------------------------*/






/* Page association Admin  JS*/



/* ========================================
   GESTION DES ONGLETS
   Au clic sur un onglet : on masque tous les
   panneaux et on affiche seulement celui cliqué
   ======================================== */
document.querySelectorAll(".onglet-btn").forEach(function (bouton) {
    bouton.addEventListener("click", function () {

        /* Désactiver tous les onglets et panneaux */
        document.querySelectorAll(".onglet-btn").forEach(function (btn) {
            btn.classList.remove("actif");
        });
        document.querySelectorAll(".onglet-panel").forEach(function (panel) {
            panel.classList.remove("actif");
        });

        /* Activer l'onglet cliqué et son panneau correspondant */
        bouton.classList.add("actif");
        const idPanel = "panel-" + bouton.getAttribute("data-onglet");
        document.getElementById(idPanel).classList.add("actif");

    });
});


/* ========================================
   OUVERTURE DE LA MODALE "VOIR DÉTAILS"
   Au clic sur un bouton "Voir détails" d'une carte,
   on injecte les données de l'association dans la modale
   puis on l'affiche
   ======================================== */
document.querySelectorAll(".btn-voir-details").forEach(function (bouton) {
    bouton.addEventListener("click", function () {

        /* Récupération des données de l'association depuis les data-attributes du bouton */
        /* Ces data-attributes sont à ajouter sur chaque bouton "Voir détails" dans le HTML */
        const nom = bouton.getAttribute("data-nom");
        const couleur = bouton.getAttribute("data-couleur");
        const couleurLight = bouton.getAttribute("data-couleur-light");
        const membres = bouton.getAttribute("data-membres");
        const reservations = bouton.getAttribute("data-reservations");
        const email = bouton.getAttribute("data-email");
        const tel = bouton.getAttribute("data-tel");

        /* Injection des données dans la modale */
        document.getElementById("details-nom").textContent = nom;
        document.getElementById("details-membres").textContent = membres;
        document.getElementById("details-reservations").textContent = reservations;
        document.getElementById("details-email").textContent = email || "-";
        document.getElementById("details-tel").textContent = tel || "-";
        document.getElementById("details-couleur-carre").style.background = couleur;
        document.getElementById("details-couleur-code").textContent = couleur;

        /* Couleur de l'icône et de l'arrière-plan selon l'association */
        document.getElementById("details-icone").style.background = couleurLight;
        document.getElementById("details-icone").querySelector("i").style.color = couleur;

        /* Couleur des avatars des membres */
        document.getElementById("details-avatar-1").style.background = couleur;
        document.getElementById("details-avatar-2").style.background = couleur;
        document.getElementById("details-avatar-3").style.background = couleur;

        /* Couleur de la bordure gauche dans l'historique */
        document.querySelectorAll(".historique-ligne").forEach(function (ligne) {
            ligne.style.borderLeftColor = couleur;
        });

        /* Réinitialiser sur l'onglet "Informations" à chaque ouverture */
        document.querySelectorAll(".onglet-btn").forEach(function (btn) {
            btn.classList.remove("actif");
        });
        document.querySelectorAll(".onglet-panel").forEach(function (panel) {
            panel.classList.remove("actif");
        });
        document.getElementById("onglet-info").classList.add("actif");
        document.getElementById("panel-info").classList.add("actif");

        /* Affichage de la modale */
        document.getElementById("overlay-details").classList.add("actif");

    });
});


/* ========================================
   FERMETURE DE LA MODALE
   Via la croix, ou en cliquant sur le fond sombre
   ======================================== */
document.getElementById("fermer-details").addEventListener("click", function () {
    document.getElementById("overlay-details").classList.remove("actif");
});

document.getElementById("overlay-details").addEventListener("click", function (event) {
    if (event.target === document.getElementById("overlay-details")) {
        document.getElementById("overlay-details").classList.remove("actif");
    }
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

document.querySelectorAll(".btn-modifier-asso").forEach(function (bouton) {

    bouton.addEventListener("click", function () {

        modifierNom.value = bouton.dataset.nom;
        modifierCouleur.value = bouton.dataset.couleur;

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

        texteSuppressionAsso.textContent =
            "Êtes-vous sûr de vouloir supprimer l'association \"" + nom + "\" ?";

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


