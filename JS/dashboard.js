document.addEventListener("DOMContentLoaded", () => {
document.getElementById("btn-menage").addEventListener("click", () => {
    fetch("index.php?page=menage")
        .then(response => response.text())
        .then(html => {
            document.querySelector(".main-content").innerHTML = html;

            
                initMenage(); // <-- APPEL APRÈS CHARGEMENT
            
        });
});
document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn_commentaire");
        if (!btn) return;

        const card = btn.closest(".card_menage");
        const zoneCommentaire = card.querySelector(".partie_commentaire");
        zoneCommentaire.classList.toggle("active");
    });
});