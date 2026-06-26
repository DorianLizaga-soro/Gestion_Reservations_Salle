
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


// Export Excel
document.getElementById("export-excel").addEventListener("click", function () {
    const periode = document.getElementById("periode-excel").value;
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "/ReservationSalle/index.php?page=export_excel";
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "periode";
    input.value = periode;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
});

// Export Google Calendar
document.getElementById("export-calendar").addEventListener("click", function () {
    const periode = document.getElementById("periode-calendar").value;
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "/ReservationSalle/index.php?page=export_calendar";
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "periode";
    input.value = periode;
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
});


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

//gestion des nombres de caractere de commentaire



const input = document.getElementById('input_commentaire');
const info = document.getElementById('info');
const max = 50;

input.addEventListener('input', () => {
    if (input.value.length > max) {
        input.value = input.value.slice(0, max);
    }
    info.textContent = `${input.value.length} / ${max} caractères`;
    info.className = input.value.length === max ? 'info warning' : 'info';
});

/* FIN de page association JS*/



/*-------------------------------------------------------------------------------*/