document.addEventListener("DOMContentLoaded", function () {

    // Compteur de caractères
    const input = document.getElementById("input-message");
    const compteur = document.querySelector(".compteur");

    if (input && compteur) {
        input.addEventListener("input", function () {
            compteur.textContent = input.value.length + "/50";
        });
    }

    // Scroll automatique en bas des messages
    const zone = document.getElementById("messages-zone");
    if (zone) {
        zone.scrollTop = zone.scrollHeight;
    }

});