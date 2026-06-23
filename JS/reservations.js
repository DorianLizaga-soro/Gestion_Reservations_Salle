/* add the Modal et le input group into the JS file and link to the HTML...

        TODO - change and verify the different needs :
        2x buttons
        2x dropdowns
        1x dateInput
        2x timestamps (Start and finish)
        1x Description (textbox/commentaire box)
        1x upload doccument (PDF)
        1x commentaire box (50car max)
        2x buttons (close modal + submit)

*/

const dropZone = document.getElementById("dropZone");
const inputFile = document.getElementById("programmePdf");
const fileNameEl = document.getElementById("programmeFileName");

const MAX_MB = 10;
const MAX_BYTES = MAX_MB * 1024 * 1024;

function setFileName(text) {
  if (fileNameEl) fileNameEl.textContent = text;
}

function showDropError(text) {
  // You can later replace this with a dedicated error <div>
  alert(text);
}

if (dropZone && inputFile) {
  // Clic sur la zone = ouvrir le sélecteur
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

  // Drag over
  dropZone.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropZone.classList.add("dragover");
  });

  // Drag leave
  dropZone.addEventListener("dragleave", () => {
    dropZone.classList.remove("dragover");
  });

  // Drop du fichier
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

    // Assign files to input + update UI
    inputFile.files = files;
    setFileName("Fichier sélectionné : " + file.name);
  });
}
      

/************************************************************
 * STEP 1: STORE ALL RESERVATIONS IN MEMORY (ARRAY)
 * This replaces static HTML rows
 ************************************************************/
let reservations = [];
let editIndex = null;

/************************************************************
 * STEP 2: SELECT DOM ELEMENTS
 ************************************************************/
const formButton = document.getElementById("createReservationBtn");
const tableBody = document.getElementById("reservationTableBody");
const filterAssociation = document.getElementById("filterAssociation");
const filterSalle = document.getElementById("filterSalle");
const filterDate = document.getElementById("filterDate");

const btnPonctuelle = document.getElementById("btnPonctuelle");
const btnReccurente = document.getElementById("btnReccurente");

/************************************************************
 * STEP 3: DEFAULT TYPE SELECTION
 ************************************************************/
let selectedType = "ponctuelle";

/************************************************************
 * STEP 4: HANDLE TYPE BUTTONS (Ponctuelle / Récurrente)
 ************************************************************/

btnPonctuelle.addEventListener("click", () => {

    selectedType = "ponctuelle";

    btnPonctuelle.classList.add("active-type");
    btnReccurente.classList.remove("active-type");

});

btnReccurente.addEventListener("click", () => {

    selectedType = "récurrente";

    btnReccurente.classList.add("active-type");
    btnPonctuelle.classList.remove("active-type");

});

/************************************************************
 * STEP 5: CREATE RESERVATION (MAIN LOGIC)
 ************************************************************/
formButton.addEventListener("click", async () => {

    /********** STEP 5.1: READ FORM VALUES **********/
    const association = document.getElementById("Asso_select").value;
    const salle = document.getElementById("salle_select").value;
    const date = document.getElementById("modalDate").value;
    const startTime = document.getElementById("modalStartTime").value;
    const endTime = document.getElementById("modalEndTime").value;
    const description = document.getElementById("modalDescription").value;
    const comment = document.getElementById("modalComment").value;

    if (
    !association ||
    !salle ||
    !date ||
    !startTime ||
    !endTime
) {
    alert("Veuillez remplir tous les champs obligatoires.");
    return;
}

async function loadReservations() {
    const res = await fetch("getReservations.php");
    reservations = await res.json();
    renderTable();
}

loadReservations();



    /********** STEP 5.2: CREATE OBJECT **********/
    const reservation = {

        association,
        salle,
        date,
        startTime,
        endTime,
        type: selectedType,
        description,
        comment

    };

    /********** STEP 5.3: SAVE INTO ARRAY **********/
        if (editIndex !== null) {
        await updateReservation(reservations[editIndex].id, reservation);
        editIndex = null;
    } else {
        await createReservation(reservation);
    }

    // Reload from DB after create/update
    await loadReservations();
    /********** STEP 5.4: RENDER TABLE **********/
  

    const modalEl = document.getElementById("staticBackdrop");

    const modal =
      bootstrap.Modal.getInstance(modalEl);

      if (modal) {
          modal.hide();
        } 

    /********** STEP 5.5: RESET FORM FIELDS **********/
    document.getElementById("Asso_select").value = "";
    document.getElementById("salle_select").value = "";
    document.getElementById("modalDate").value = "";
    document.getElementById("modalStartTime").value = "";
    document.getElementById("modalEndTime").value = "";
    document.getElementById("modalDescription").value = "";
    document.getElementById("modalComment").value = "";

});

/************************************************************
 * STEP 6: RENDER TABLE (ARRAY → HTML)
 ************************************************************/
function renderTable() {

    /********** CLEAR TABLE FIRST **********/
   let html = "";

    /********** LOOP THROUGH RESERVATIONS **********/
    reservations.forEach((r, index) => {

         html += `
            <tr>

                <td>${formatDate(r.date)}</td>

                <td>${r.startTime} - ${r.endTime}</td>

                <td>${getAssociationName(r.association)}</td>

                <td>
                    <span class="badge">
                        ${r.type}
                    </span>
                </td>

                <td>${getSalleName(r.salle)}</td>

                <td>-</td>

                <td>
                    <span class="badge confirmed">
                        Confirmée
                    </span>
                </td>

                <td class="actions">

                    <button onclick="viewReservation(${r.id})">Voir</button>

                    <button onclick="modifyReservation(${r.id})">Modify</button>

                    <button onclick="deleteReservation(${r.id})">Annuler</button>

                </td>

            </tr>
        `;
    });
    tableBody.innerHTML = html;
}

async function createReservation(reservation) {
    await fetch("addReservations.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(reservation)
    });

    await loadReservations(); // refresh table from BDD
}

async function updateReservation(id, reservation) {
    await fetch("updateReservation.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, ...reservation })
    });

    await loadReservations();
}


/************************************************************
 * STEP 7: DELETE RESERVATION
 ************************************************************/
async function deleteReservation(id) {
    await fetch("deleteReservation.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
    });

    await loadReservations();
}

/************************************************************
 * STEP 8: Modify RESERVATION (OPTIONAL DEBUG)
 ************************************************************/
function modifyReservation(id) {

    const r = reservations.find(res => res.id == id);
    editIndex = reservations.findIndex(res => res.id == id);
    if (!r) { alert("Reservation introuvable");
      return;  }

    // fill form fields
    document.getElementById("Asso_select").value = r.association;
    document.getElementById("salle_select").value = r.salle;
    document.getElementById("modalDate").value = r.date;
    document.getElementById("modalStartTime").value = r.startTime;
    document.getElementById("modalEndTime").value = r.endTime;
    document.getElementById("modalDescription").value = r.description;
    document.getElementById("modalComment").value = r.comment;

    // set type UI
    selectedType = r.type;

    if (r.type === "ponctuelle") {
        btnPonctuelle.classList.add("active-type");
        btnReccurente.classList.remove("active-type");
    } else {
        btnReccurente.classList.add("active-type");
        btnPonctuelle.classList.remove("active-type");
    }

    // open modal
    const modalEl = document.getElementById("staticBackdrop");
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

/************************************************************
 * STEP 8: VIEW RESERVATION (OPTIONAL DEBUG)
 ************************************************************/
function viewReservation(index) {

    console.log(reservations[index]);

}

/************************************************************
 * STEP 9: FORMAT HELPERS
 ************************************************************/
function formatDate(dateStr) {

    if (!dateStr) return "";

    const date = new Date(dateStr);

    return date.toLocaleDateString("fr-FR", {
        day: "2-digit",
        month: "long"
    });

}

function getAssociationName(id) {

    const map = {
        "1": "Club de Tennis Monestié",
        "2": "Association Culturelle Les Arts",
        "3": "Amicale des Retraités",
        "4": "Club de Pétanque du Terroir",
        "5": "Danse Traditionnelle Occitane",
        "6": "Association Parents d'Élèves",
        "7": "Chorale Voix du Sud",
        "8": "Club Informatique Senior",
        "9": "Yoga & Bien-être"
    };

    return map[id] || "—";

}

function getSalleName(id) {

    const map = {
        "1": "Réfectoire",
        "2": "Bar",
        "3": "Salle de réunion",
        "4": "Toutes les salles"
    };

    return map[id] || "—";

}



