window.addEventListener("load", initMessagerie);


function initMessagerie() {
   
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

    document.querySelectorAll(".conversation-item").forEach(item => {
    item.addEventListener("click", () => {

        const id = item.dataset.id;

        // 🔥 Récupérer le nombre de non lus dans cette conversation
        const badge = item.querySelector(".conv-badge");
        let nbConvNonLus = badge ? parseInt(badge.textContent) : 0;

        // 🔥 Supprimer le badge de la conversation
        if (badge) badge.remove();

        

        // 🔥 Charger la conversation via AJAX
        fetch(`./index.php?page=messagerie_ajax&reservation=${id}`)
            .then(res => res.json())
            .then(data => {

                document.querySelector("#messages-entete").innerHTML = data.entete;
                document.querySelector("#messages-zone").innerHTML = data.messages;
                document.querySelector(".messages-footer").outerHTML = data.footer;

                const zone = document.querySelector("#messages-zone");
                zone.scrollTop = zone.scrollHeight;

                // 🔥 Mettre à jour le badge global
        const badgeGlobal = document.querySelector("#badge-global-nonlus");
        if (badgeGlobal) {
            let nbGlobal = parseInt(badgeGlobal.textContent);
            let nouveauTotal = nbGlobal - nbConvNonLus;

            badgeGlobal.textContent = nouveauTotal;

            if (nouveauTotal <= 0) {
                badgeGlobal.style.display = "none";
            }
        }

                
            });
    });
});


    document.addEventListener("submit", function(e) {
    const form = e.target;

    if (form.id === "form-message") {
        e.preventDefault();

        const formData = new FormData(form);
        const id = formData.get("id_reservation");

        fetch("./index.php?page=envoyer_message", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(() => {

            // Recharge la conversation via AJAX
            fetch(`./index.php?page=messagerie_ajax&reservation=${id}`)
                .then(res => res.json())
                .then(data => {

                    document.querySelector("#messages-entete").innerHTML = data.entete;
                    document.querySelector("#messages-zone").innerHTML = data.messages;
                    document.querySelector(".messages-footer").outerHTML = data.footer;

                    const zone = document.querySelector("#messages-zone");
                    zone.scrollTop = zone.scrollHeight;
                });
        });

        // Vide le champ
        document.querySelector("#input-message").value = "";
        document.querySelector(".compteur").textContent = "0/50";
    }
});
 

}