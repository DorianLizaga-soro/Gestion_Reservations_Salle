function initReservation() {

 // Création d'une seule instance de la modale





    /*************************************************************
     * DROPZONE PDF
     *************************************************************/
    const dropZone = document.getElementById("dropZone");
    const pdfZone = document.getElementById("pdfViewerZone");
    const pdfBtn = document.getElementById("pdfViewerBtn");

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

        dropZone.addEventListener("click", () => inputFile.click());

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

            const file = e.dataTransfer.files[0];
            if (!file) return;

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

            inputFile.files = e.dataTransfer.files;
            setFileName("Fichier sélectionné : " + file.name);
        });
    }


    /*************************************************************
     * FILTRES TABLEAU
     *************************************************************/
    const searchInput = document.getElementById("searchBar");
    const selectAssociation = document.getElementById("associationFilter");
    const selectType = document.getElementById("typeFilter");
    const selectStatut = document.getElementById("statutFilter");

    const reservations = document.querySelectorAll("#reservationTableBody tr");

    function filterReservations() {
        const searchValue = searchInput.value.toLowerCase();
        let associationValue = selectAssociation.options[selectAssociation.selectedIndex].text.toLowerCase();
        if (associationValue === "toutes les associations") associationValue = "";

        const typeValue = selectType.value.toLowerCase();
        const statutValue = selectStatut.value.toLowerCase();

        reservations.forEach(reservation => {
            const cols = reservation.querySelectorAll("td");

            const date = cols[0].textContent.toLowerCase();
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

            const matchAssociation = associationValue === "" || association.includes(associationValue);
            const matchType = typeValue === "" || type.includes(typeValue);
            const matchStatut = statutValue === "" || statut.includes(statutValue);

            reservation.style.display = (matchSearch && matchAssociation && matchType && matchStatut)
                ? ""
                : "none";
        });
    }

    searchInput.addEventListener("input", filterReservations);
    selectAssociation.addEventListener("change", filterReservations);
    selectType.addEventListener("change", filterReservations);
    selectStatut.addEventListener("change", filterReservations);


    /*************************************************************
     * BOUTON NOUVELLE RÉSERVATION — RESET FORMULAIRE
     *************************************************************/
    document.getElementById("btnAdd").addEventListener("click", () => {

    const form = document.getElementById("reservationForm");

    form.reset();

    form.querySelectorAll("input, select, textarea").forEach(el => {
        el.value = "";
        el.disabled = false;
    });

    // Réinitialiser les boutons Ponctuelle / Récurrente
    document.querySelectorAll('.reservation-type').forEach(b => {
        b.classList.remove('active-type');
        if (b.dataset.type === "ponctuelle") b.classList.add('active-type');
        b.disabled = false;
    });

    

    dropZone.style.display = "block";
    pdfZone.style.display = "none";
    pdfBtn.href = "#";

    // Champ caché et action
    document.getElementById("id_reservation_hidden").value = "";
    form.setAttribute("action", "./index.php?page=createreservation");

    // Titres / bouton
    document.querySelector(".modal-header .mb-1").innerText = "Nouvelle réservation";
    document.querySelector(".modal-header .text-muted").innerText = "Créer une réservation de salle";
    document.getElementById("createReservationBtn").innerText = "Créer la réservation";

    const modalEl = document.getElementById("staticBackdrop");
    const modal = new bootstrap.Modal(modalEl);

    // Nettoyage automatique du backdrop
    modalEl.addEventListener("hidden.bs.modal", () => {
        document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
        document.body.classList.remove("modal-open");
    });
    modal.show();


});



    /*************************************************************
     * VOIR / MODIFIER
     *************************************************************/
    function remplirModale(id_association, id_salle, type, date_, heure_debut, heure_fin, description, commentaire, pdf, pdfName, mode, menageName) {
        document.getElementById("Asso_select").value = id_association;
        document.getElementById("salle_select").value = id_salle;
        document.getElementById("modalType").value = type;
        document.getElementById("modalDate").value = date_;
        document.getElementById("modalStartTime").value = heure_debut;
        document.getElementById("modalEndTime").value = heure_fin;
        document.getElementById("modalDescription").value = description ?? "";
        document.getElementById("modalComment").value = commentaire ?? "";
       document.getElementById("menage").value = menageName ?? "";




        document.querySelectorAll('.reservation-type').forEach(b => {
            b.classList.toggle('active-type', b.dataset.type === type);
        });

       if (mode === "voir") {
    dropZone.style.display = "none";
    pdfZone.style.display = "block";
    pdfBtn.href = pdf;
    fileNameEl.textContent = pdfName || "Aucun PDF";
    inputFile.disabled = true;
      }

      if (mode === "modifier") {
          dropZone.style.display = "block";
          pdfZone.style.display = "none";
          fileNameEl.textContent = pdfName ? "PDF actuel : " + pdfName : "Aucun fichier sélectionné";
          inputFile.disabled = false;
      }


      
    }

    function basculerLectureSeule(lectureSeule) {
        const champs = [
            "Asso_select", "salle_select", "modalDate", "modalStartTime",
            "modalEndTime", "modalDescription", "modalComment", "programmePdf","menage"
        ];

        champs.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.disabled = lectureSeule;
        });

        document.querySelectorAll('.reservation-type').forEach(b => b.disabled = lectureSeule);

        const boutonValider = document.querySelector("#reservationForm button[type='submit']");
        if (boutonValider) boutonValider.style.display = lectureSeule ? "none" : "";
    }

    document.body.addEventListener("click", (e) => {

        const bouton = e.target.closest(".btn-secondary[data-id]");
        if (!bouton) return;

        const id = bouton.dataset.id;
        const estModification = bouton.textContent.trim() === "Modifier";



        document.getElementById("id_reservation_hidden").value = id;

        remplirModale(
            bouton.dataset.association,
            bouton.dataset.salle,
            bouton.dataset.type,
            bouton.dataset.date,
            bouton.dataset.debut,
            bouton.dataset.fin,
            bouton.dataset.description,
            bouton.dataset.commentaire,
            bouton.dataset.pdf,
            bouton.dataset.pdfName,
            estModification ? "modifier" : "voir",
            bouton.dataset.menageId

          );

        basculerLectureSeule(!estModification);

        if (estModification) {
            document.querySelector(".modal-header .mb-1").innerText = "Modifier la réservation";
            document.querySelector(".modal-header .text-muted").innerText = "Modifier la réservation de salle";
            document.getElementById("createReservationBtn").innerText = "Modifier la réservation";
            document.getElementById("reservationForm").setAttribute("action", "./index.php?page=changeReservation");
        } else {
            document.querySelector(".modal-header .mb-1").innerText = "Voir la réservation";
            document.querySelector(".modal-header .text-muted").innerText = "Informations de la réservation";
            document.getElementById("createReservationBtn").innerText = "Fermer";
        }

        const modalEl = document.getElementById("staticBackdrop");
        const modal = new bootstrap.Modal(modalEl);

        // Nettoyage automatique du backdrop
        modalEl.addEventListener("hidden.bs.modal", () => {
            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
            document.body.classList.remove("modal-open");
        });
        modal.show();

    });


    const btnAdd = document.getElementById("btnAdd");
const modalEl = document.getElementById("staticBackdrop");

if (btnAdd && modalEl) {
    btnAdd.addEventListener("click", () => {
        const form = document.getElementById("reservationForm");

        // Vider le formulaire
        form.reset();
        form.querySelectorAll("input, select, textarea").forEach(el => {
            el.value = "";
            el.disabled = false;
        });

        // Type par défaut
        document.getElementById("modalType").value = "ponctuelle";
        document.querySelectorAll(".reservation-type").forEach(b => {
            b.classList.remove("active-type");
            if (b.dataset.type === "ponctuelle") b.classList.add("active-type");
            b.disabled = false;
        });

        // Champ caché et action
        document.getElementById("id_reservation_hidden").value = "";
        form.setAttribute("action", "./index.php?page=createreservation");

        // Titres / bouton
        document.querySelector(".modal-header .mb-1").innerText = "Nouvelle réservation";
        document.querySelector(".modal-header .text-muted").innerText = "Créer une réservation de salle";
        document.getElementById("createReservationBtn").innerText = "Créer la réservation";

        // Ouvrir la modale
        new bootstrap.Modal(modalEl).show();
    });
}

}
