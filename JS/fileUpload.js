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

    for (let pair of formData.entries()) {
    console.log(pair[0] + ": " + pair[1]);
}

  fetch("PHP/codes/createReservation.php", {
    method: "POST",
    body: formData,
    credentials: "include"
})

    .then(response => response.json())
    .then(data => {
      // AJOUT : avant, on affichait toujours "Réservation créée",
      // même quand data.success était false (réponse jamais vérifiée)
      if (!data.success) {
        alert("Erreur : " + (data.error || "création impossible"));
        return;
      }

      alert("Réservation créée");

      document.getElementById("reservationForm").reset();

      bootstrap.Modal.getInstance(
        document.getElementById("staticBackdrop")
      ).hide();

      location.reload();
    })
    .catch(error => {
      console.error(error);
      alert("Erreur lors de la création");
    });
});
