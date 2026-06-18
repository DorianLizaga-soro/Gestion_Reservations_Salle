//boutton OK afficher la partie ajouter commentaire

const btn = document.getElementById("afficherComm");
const divCommentaire = document.querySelector(".partie_commentaire");

btn.addEventListener("click", () => {
    divCommentaire.classList.toggle("active");
});

function deconnexion(){
    if (confirm("Souhaitez-vous vous déconnecter ?")){
        window.location.href = '/html/connexion.html';
    }
}

