const dropZone = document.getElementById("dropZone");
const inputFile = document.getElementById("programmePdf");

// Clic sur la zone = ouvrir le sélecteur
dropZone.addEventListener("click", () => {
    inputFile.click();
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

    inputFile.files = e.dataTransfer.files;

    dropZone.querySelector("p").textContent = "Fichier sélectionné : " + inputFile.files[0].name;
});
