<?php

require_once(__DIR__ . '/connexionBDD.php');


if (!isset($_SESSION["id"])) {
    header("Location: /index.php?page=login");
    exit;
}

//salle
$stmt = $conn->prepare("SELECT nom FROM salles WHERE id = ?");
$stmt->execute([1]); 
$Salle = $stmt->fetch(PDO::FETCH_ASSOC);


// association
$stmt = $conn->prepare("SELECT nom, id_responsable FROM associations WHERE id = ?");
$stmt->execute([1]);
$association = $stmt->fetch(PDO::FETCH_ASSOC);

$nomAssos = $association["nom"];
$idResponsable = $association["id_responsable"];

// nom du responsable
$stmt = $conn->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
$stmt->execute([$idResponsable]);
$nomResponsable = $stmt->fetchColumn();




// carte reservations


$stmt = $conn->prepare("SELECT * FROM reservations");
$stmt->execute();
$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (!$reservation) {
    $reservation = [];
}


$cartesHTML = "";

foreach($reservation as $r) {

    $stmt = $conn->prepare("SELECT nom FROM salles WHERE id = ?");
$stmt->execute([$r["id_salle"]]);
$nomSalle = $stmt->fetchColumn();



    $badge = match($r["statut"]) {
        "en_attente" => "En attente",
        "validee" => "Valider",
        "annulee" => "Annuler",
        default => "Inconnu"
    };

    $carte = "
   <div class='carte-reservation'>

                <button class='entete-bouton' data-cible='panneau-1'>
                    <div class='entete-gauche'>
                        <div class='barre-couleur'></div>
                        <div class='date-heure'>
                            <p>{$r["date_"]}</p>
                            <p>{$r["heure_debut"]},{$r["heure_fin"]}</p>
                        </div>
                        <div class='titre-salle'>
                            <p>{{motifdelaresa}}</p>
                            <p>{$nomSalle}</p>
                        </div>
                    </div>
                    <div class='entete-droite'>
                        <span class='statut-badge'>{$badge}</span>
                        <span class='chevron'>&#9660;</span>
                    </div>
                </button>

                <div class='panneau' id='panneau-1'>
                    <div class='panneau-contenu'>

                        <div class='boutons-action'>
                            <button class='btn-modifier'>Modifier</button>
                            <button class='btn-annuler'>Annuler</button>

                            <form method='post' action='ajouter_fichier.php' enctype='multipart/form-data'
                                style='display:inline-block;'>
                                <input type='hidden' name='id_reservation' value='1'>
                                <label class='label-fichier' for='fichier-1'>
                                    📎 Ajouter PDF
                                </label>
                                <input type='file' name='fichier' id='fichier-1' class='input-fichier-cache'
                                    accept='application/pdf'>
                                <span class='nom-fichier-selectionne' id='nom-fichier-1'></span>
                            </form>
                        </div>

                        <div class='commentaire-box'>
                            Commentaire : {motif}
                        </div>

                        <p class='messages-label'>Messages</p>

                        <div class='message'>
                            <div class='avatar'>{{initial}}</div>
                            <div class='message-contenu'>
                                <p><span class='auteur'>{{nom,prenom}}</span> <span class='date-message'>{{date,heuredumessage}}</span></p>
                                <p>{{commentaire}}</p>
                            </div>
                        </div>

                       
                        <div class='ajout-commentaire'>
                            <input type='text' placeholder='Ajouter un commentaire... (50 car.)' maxlength='50'>
                            <button>Envoyer</button>
                        </div>

                    </div>
                </div>

            </div>
    ";

    $cartesHTML .= $carte;
}

$variables = [
    "{{nomassociation}}" => $nomAssos,
    "{{carteReservation}}" => $cartesHTML
];


$template = file_get_contents(__DIR__ . '/../../html/association.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);


echo $page;