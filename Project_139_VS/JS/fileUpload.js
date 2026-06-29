document.addEventListener("DOMContentLoaded", () => {

const dropZone = document.getElementById("dropZone");
const inputFile = document.getElementById("programmePdf");
const fileNameEl = document.getElementById("programmeFileName");

const MAX_MB = 10;
const MAX_BYTES = MAX_MB * 1024 * 1024;

function setFileName(text) {
  if (fileNameEl) fileNameEl.textContent = text;
}

function showDropError(text) {
  alert(text);
}

if (dropZone && inputFile) {
  // Click zone -> open file dialog
  dropZone.addEventListener("click", () => {
    inputFile.click();
  });

  inputFile.addEventListener("change", () => {
    const file = inputFile.files?.[0];
    if (!file) return;

    if (file.type !== "application/pdf") {
      showDropError("Le fichier doit être un PDF.");
      inputFile.value = "";
      setFileName("Aucun fichier sélectionné");
      return;
    }

    if (file.size > MAX_BYTES) {
      showDropError(`Taille maximale : ${MAX_MB} MB.`);
      inputFile.value = "";
      setFileName("Aucun fichier sélectionné");
      return;
    }

    setFileName("Fichier sélectionné : " + file.name);
  });

  dropZone.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropZone.classList.add("dragover");
  });

  dropZone.addEventListener("dragleave", () => {
    dropZone.classList.remove("dragover");
  });

  dropZone.addEventListener("drop", (e) => {
    e.preventDefault();
    dropZone.classList.remove("dragover");

    const files = e.dataTransfer.files;
    if (!files || files.length === 0) return;

    const file = files[0];

    if (file.type !== "application/pdf") {
      showDropError("Le fichier doit être un PDF.");
      setFileName("Aucun fichier sélectionné");
      return;
    }

    if (file.size > MAX_BYTES) {
      showDropError(`Taille maximale : ${MAX_MB} MB.`);
      setFileName("Aucun fichier sélectionné");
      return;
    }

    inputFile.files = files;
    setFileName("Fichier sélectionné : " + file.name);
  });
}

});


// --- SEARCH + FILTRES RÉSERVATIONS ---
const searchInput = document.getElementById("searchBar");
const selectAssociation = document.getElementById("associationFilter");
const selectType = document.getElementById("typeFilter");
const selectStatut = document.getElementById("statutFilter");

const reservations = document.querySelectorAll("#reservationTableBody tr");

function filterReservations() {

    const searchValue = searchInput.value.toLowerCase();
    const associationValue = selectAssociation.value.toLowerCase();
    const typeValue = selectType.value.toLowerCase();
    const statutValue = selectStatut.value.toLowerCase();

    reservations.forEach(reservation => {

        const cols = reservation.querySelectorAll("td");

        const date = cols[0].textContent.toLowerCase();
        const heure = cols[1].textContent.toLowerCase();
        const association = cols[2].textContent.toLowerCase();
        const type = cols[3].textContent.toLowerCase();
        const salle = cols[4].textContent.toLowerCase();
        const responsable = cols[5].textContent.toLowerCase();
        const statut = cols[6].textContent.toLowerCase();

        const matchSearch =
            date.includes(searchValue) ||
            association.includes(searchValue) ||
            salle.includes(searchValue) ||
            responsable.includes(searchValue);

        const matchAssociation =
            associationValue === "" || association.includes(associationValue);

        const matchType =
            typeValue === "" || type.includes(typeValue);

        const matchStatut =
            statutValue === "" || statut.includes(statutValue);

        if (matchSearch && matchAssociation && matchType && matchStatut) {
            reservation.style.display = "";
        } else {
            reservation.style.display = "none";
        }
    });
}

// --- EVENTS ---
searchInput.addEventListener("input", filterReservations);
selectAssociation.addEventListener("change", filterReservations);
selectType.addEventListener("change", filterReservations);
selectStatut.addEventListener("change", filterReservations);


/*************************************************************
 * SUBMIT FORM FOR MODAL + BUTTON CREER NEW RES
 * ***********************************************************
 */

document.querySelectorAll('.reservation-type').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.reservation-type').forEach(b => b.classList.remove('active-type'));
    btn.classList.add('active-type');
    // btn.dataset.type lit l'attribut data-type="ponctuelle" / "recurrente"
    // -> il faut l'ajouter sur les boutons #btnPonctuelle / #btnReccurente dans reservation.html
    document.getElementById('modalType').value = btn.dataset.type;
  });
});


document.getElementById("reservationForm").addEventListener("submit", function (e) {
  e.preventDefault();


  if (!document.getElementById("Asso_select").value ||
    !document.getElementById("salle_select").value ||
    !document.getElementById("modalDate").value ||
    !document.getElementById("modalStartTime").value ||
    !document.getElementById("modalEndTime").value) {

    alert("Veuillez remplir tous les champs obligatoires.");
    return;
}

  const form = document.getElementById("reservationForm");
  const formData = new FormData(form);

  formData.append("id_association", document.getElementById("Asso_select").value);
  formData.append("id_salle", document.getElementById("salle_select").value);
  // AJOUT : ce champ manquait, createReservation.php rejetait systématiquement
  // la requête avec "Champ manquant : type"
  formData.append("type", document.getElementById("modalType").value);
  formData.append("date_", document.getElementById("modalDate").value);
  formData.append("heure_debut", document.getElementById("modalStartTime").value);
  formData.append("heure_fin", document.getElementById("modalEndTime").value);
  formData.append("description", document.getElementById("modalDescription").value);
  formData.append("commentaire", document.getElementById("modalComment").value);

  const pdf = document.getElementById("programmePdf").files[0];

  if (pdf) {
    formData.append("programmePdf", pdf);
  }

  // AJOUT : si le formulaire a été ouvert via "Modifier" (voir plus bas),
  // form.dataset.idReservation contient l'id à mettre à jour -> on poste
  // vers modifierReservation.php plutôt que creerReservation.php.
  const idReservation = form.dataset.idReservation;

  if (idReservation) {
    formData.append("id_reservation", idReservation);
  }

  const url = idReservation
    ? "http://localhost/Project_139_VS/PHP/codes/modifierReservation.php"
    : "http://localhost/Project_139_VS/PHP/codes/createReservation.php";

    for (let pair of formData.entries()) {
    console.log(pair[0] + ": " + pair[1]);
}

  fetch(url, {
    method: "POST",
    body: formData,
    credentials: "include"
})

    .then(response => response.json())
    .then(data => {
      // AJOUT : avant, on affichait toujours "Réservation créée",
      // même quand data.success était false (réponse jamais vérifiée)
      if (!data.success) {
        alert("Erreur : " + (data.error || (idReservation ? "modification impossible" : "création impossible")));
        return;
      }

      alert(idReservation ? "Réservation modifiée" : "Réservation créée");

      document.getElementById("reservationForm").reset();
      // AJOUT : on efface l'id mémorisé pour repartir en mode création
      // au prochain ouverture de la modale.
      form.dataset.idReservation = "";
      basculerLectureSeule(false);

      bootstrap.Modal.getInstance(
        document.getElementById("staticBackdrop")
      ).hide();

      location.reload();
    })
    .catch(error => {
      console.error(error);
      alert(idReservation ? "Erreur lors de la modification" : "Erreur lors de la création");
    });
});


/*************************************************************
 * AJOUT : VOIR / MODIFIER / SUPPRIMER
 * Les lignes du tableau sont injectées côté serveur (index.php),
 * donc on délègue les clics sur document.body plutôt que d'attacher
 * un listener par bouton (qui n'existerait pas encore au chargement).
 * ***********************************************************
 */

function remplirModale(reservation) {
  document.getElementById("Asso_select").value = reservation.id_association;
  document.getElementById("salle_select").value = reservation.id_salle;
  document.getElementById("modalType").value = reservation.type;
  document.getElementById("modalDate").value = reservation.date_;
  document.getElementById("modalStartTime").value = reservation.heure_debut;
  document.getElementById("modalEndTime").value = reservation.heure_fin;
  document.getElementById("modalDescription").value = reservation.description ?? "";
  document.getElementById("modalComment").value = reservation.commentaire ?? "";

  document.querySelectorAll('.reservation-type').forEach(b => {
    b.classList.toggle('active-type', b.dataset.type === reservation.type);
  });
}

function basculerLectureSeule(lectureSeule) {
  const champs = [
    "Asso_select", "salle_select", "modalDate", "modalStartTime",
    "modalEndTime", "modalDescription", "modalComment", "programmePdf"
  ];

  champs.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.disabled = lectureSeule;
  });

  document.querySelectorAll('.reservation-type').forEach(b => b.disabled = lectureSeule);

  const form = document.getElementById("reservationForm");
  const boutonValider = form.querySelector('button[type="submit"]');
  if (boutonValider) boutonValider.style.display = lectureSeule ? "none" : "";
}

const modaleReservation = document.getElementById("staticBackdrop");

if (modaleReservation) {
  modaleReservation.addEventListener("show.bs.modal", (e) => {
    // e.relatedTarget = élément qui a déclenché l'ouverture.
    // - Renseigné par Bootstrap quand on clique sur le bouton "Nouvelle
    //   réservation" (data-bs-toggle="modal") -> on repart d'un formulaire vide.
    // - Absent quand l'ouverture vient de notre code (Voir/Modifier ci-dessous,
    //   qui appelle .show() en JS) -> on ne touche à rien, remplirModale()
    //   vient déjà de faire le travail.
    if (e.relatedTarget) {
      document.getElementById("reservationForm").reset();
      document.getElementById("reservationForm").dataset.idReservation = "";
      basculerLectureSeule(false);
    }
  });
}

document.body.addEventListener("click", (e) => {

  const boutonVoirOuModifier = e.target.closest(".btn-secondary[data-id]");
  const boutonSupprimer = e.target.closest(".btn-danger[data-id]");

  if (!boutonVoirOuModifier && !boutonSupprimer) return;

  const id = (boutonVoirOuModifier || boutonSupprimer).dataset.id;

  if (boutonSupprimer) {
    if (!confirm("Supprimer cette réservation ?")) return;

    const data = new FormData();
    data.append("id", id);

    fetch("http://localhost/Project_139_VS/PHP/codes/supprimerReservation.php", {
      method: "POST",
      body: data,
      credentials: "include"
    })
      .then(r => r.json())
      .then(reponse => {
        if (!reponse.success) {
          alert("Erreur : " + (reponse.error || "suppression impossible"));
          return;
        }
        location.reload();
      })
      .catch(err => {
        console.error(err);
        alert("Erreur lors de la suppression");
      });

    return;
  }

  // "Voir" et "Modifier" ont la même classe btn-secondary : on les
  // distingue par leur libellé affiché plutôt que d'ajouter une classe
  // de plus dans index.php.
  const estModification = boutonVoirOuModifier.textContent.trim() === "Modifier";

  fetch(`http://localhost/Project_139_VS/PHP/codes/lireReservation.php?id=${id}`, {
    credentials: "include"
  })
    .then(r => r.json())
    .then(reponse => {
      if (!reponse.success) {
        alert("Erreur : " + (reponse.error || "réservation introuvable"));
        return;
      }

      remplirModale(reponse.reservation);
      basculerLectureSeule(!estModification);

      const form = document.getElementById("reservationForm");
      form.dataset.idReservation = estModification ? id : "";

      new bootstrap.Modal(document.getElementById("staticBackdrop")).show();
    })
    .catch(err => {
      console.error(err);
      alert("Erreur lors du chargement de la réservation");
    });
});
