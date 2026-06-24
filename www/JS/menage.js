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
 
//gestion nombre de caractere commentaire

const input = document.getElementById('input_commentaire');
  const info = document.getElementById('info');
  const max = 50;

  input.addEventListener('input', () => {
    if (input.value.length > max) {
      input.value = input.value.slice(0, max); 
    }
    info.textContent = `${input.value.length} / ${max} caractères`;
    info.className = input.value.length === max ? 'info warning' : 'info';
  });


//partie deconnexion
function deconnexion(){
    if (confirm("Souhaitez-vous vous déconnecter ?")){
        window.location.href = '/html/connexion.html';
    }
}

//filtre btn 

const btnTous = document.getElementById("btn_tous");
const btnFaire = document.getElementById("btn_faire");
const btnAttente = document.getElementById("btn_attente");

const cartes = document.querySelectorAll(".card_menage");

function filtrer(statuts) {
    cartes.forEach(carte => {
        const statut = carte.getAttribute("data-statut");

        if (statuts.includes(statut)) {
            carte.style.display = "block";
        } else {
            carte.style.display = "none";
        }
    });
}

btnTous.addEventListener("click", () => {
    filtrer(["a_faire", "attente"]);
});

btnFaire.addEventListener("click", () => {
    filtrer(["a_faire"]);
});

btnAttente.addEventListener("click", () => {
    filtrer(["attente"]);
});
