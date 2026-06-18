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
