//boutton OK afficher la partie ajouter commentaire

document.addEventListener("DOMContentLoaded", () => {

    document.addEventListener("click", function(e) {
        const btn = e.target.closest(".btn_commentaire");
        if (!btn) return;

        const card = btn.closest(".card_menage");
        const zoneCommentaire = card.querySelector(".partie_commentaire");
        zoneCommentaire.classList.toggle("active");
    });

});




//partie deconnexion
function deconnexion(){
    if (confirm("Souhaitez-vous vous déconnecter ?")){
        window.location.href = '/html/connexion.html';
    }
}

