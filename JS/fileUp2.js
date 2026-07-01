
function initReservation() {

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




// --- SEARCH + FILTRES RÉSERVATIONS ---
const searchInput = document.getElementById("searchBar");
const selectAssociation = document.getElementById("associationFilter");
const selectType = document.getElementById("typeFilter");
const selectStatut = document.getElementById("statutFilter");

const reservations = document.querySelectorAll("#reservationTableBody tr");

function filterReservations() {

    const searchValue = searchInput.value.toLowerCase();
    let associationValue= selectAssociation.options[selectAssociation.selectedIndex].text.toLowerCase();
    if (associationValue==="toutes les associations") associationValue="";

  
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



/*************************************************************
 * AJOUT : VOIR / MODIFIER / SUPPRIMER
 * Les lignes du tableau sont injectées côté serveur (index.php),
 * donc on délègue les clics sur document.body plutôt que d'attacher
 * un listener par bouton (qui n'existerait pas encore au chargement).
 * ***********************************************************
 */

function remplirModale(id_association,id_salle,type,date_,heure_debut,heure_fin,description,commentaire) {
  document.getElementById("Asso_select").value = id_association;
  document.getElementById("salle_select").value = id_salle;
  document.getElementById("modalType").value = type;
  document.getElementById("modalDate").value = date_;
  document.getElementById("modalStartTime").value = heure_debut;
  document.getElementById("modalEndTime").value = heure_fin;
  document.getElementById("modalDescription").value = description ?? "";
  document.getElementById("modalComment").value = commentaire ?? "";

  document.querySelectorAll('.reservation-type').forEach(b => {
    b.classList.toggle('active-type', b.dataset.type === type);
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

document.body.addEventListener("click", (e) => {

  const boutonVoirOuModifier = e.target.closest(".btn-secondary[data-id]");
  const boutonSupprimer = e.target.closest(".btn-danger");

  if (!boutonVoirOuModifier && !boutonSupprimer) return;

  const id = (boutonVoirOuModifier || boutonSupprimer).dataset.id;

  

  // Voir / Modifier
  document.getElementById("id_reservation_hidden").value = id;

  const estModification = boutonVoirOuModifier.textContent.trim() === "Modifier";

  // Remplir la modale
  remplirModale(
      boutonVoirOuModifier.dataset.association,
      boutonVoirOuModifier.dataset.salle,
      boutonVoirOuModifier.dataset.type,
      boutonVoirOuModifier.dataset.date,
      boutonVoirOuModifier.dataset.debut,
      boutonVoirOuModifier.dataset.fin,
      boutonVoirOuModifier.dataset.description,
      boutonVoirOuModifier.dataset.commentaire
  );

  basculerLectureSeule(!estModification);

  // 🔥 CHANGEMENT DU TITRE + BOUTON
  if (estModification) {
      changeButtonTitle();
  } else {
      document.querySelector(".modal-header .mb-1").innerText = "Voir la réservation";
      document.querySelector(".modal-header .text-muted").innerText = "Informations de la réservation";
      document.getElementById("createReservationBtn").innerText = "Fermer";
  }

  new bootstrap.Modal(document.getElementById("staticBackdrop")).show();
});



function showModal(readonly) {
    basculerLectureSeule(readonly);
    new bootstrap.Modal(document.getElementById("staticBackdrop")).show();
}


function changeButtonTitle() {
  
  document.querySelector(".modal-header .mb-1").innerText="Modifier la réservation";
  document.querySelector(".modal-header .text-muted").innerText="Modifier la réservation de salle";
  document.getElementById("createReservationBtn").innerText="Modifier la réservation";

  document.getElementById("reservationForm").setAttribute("action","./index.php?page=changeReservation");
}


document.getElementById("btnAdd").addEventListener("click",function(e) {
  document.getElementById("id_reservation_hidden").value="";
  document.querySelector(".modal-header .mb-1").innerText="Ajouter la réservation";
  document.querySelector(".modal-header .text-muted").innerText="Créer la réservation de salle";
  document.getElementById("createReservationBtn").innerText="Ajouter la réservation";
  document.getElementById("reservationForm").setAttribute("action","./index.php?page=createreservation");
  new bootstrap.Modal(document.getElementById("staticBackdrop")).show();
})
}