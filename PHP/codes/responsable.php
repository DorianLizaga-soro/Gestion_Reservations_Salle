<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: /ReservationSalle/index.php?page=login");
    exit;
}

// commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commentaire'])) {
    $stmt = $conn->prepare("INSERT INTO commentaires (id_reservation, id_auteur, contenu, date_creation) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['id_reservation'],
        $_SESSION["id"],
        $_POST['commentaire'],
        date("Y-m-d H:i:s")
    ]);
    header("Location: /ReservationSalle/index.php?page=responsable");
    exit;
}

// association de l'utilisateur connecté
$stmt = $conn->prepare("SELECT nom, id_responsable, couleur FROM associations WHERE id = ?");
$stmt->execute([$_SESSION["id_association"]]);
$association = $stmt->fetch(PDO::FETCH_ASSOC);

$nomAssos = $association["nom"];
$idResponsable = $association["id_responsable"];
$couleur = $association["couleur"] ?? "#35CA6C";

// ===== STATISTIQUES =====

$moisActuel = date("Y-m");

// Nombre total de réservations du mois
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservations 
    WHERE id_association = ? AND DATE_FORMAT(date_, '%Y-%m') = ?");
$stmt->execute([$_SESSION["id_association"], $moisActuel]);
$nbResaMois = $stmt->fetchColumn();

// Nombre de réservations récurrentes du mois
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservations 
    WHERE id_association = ? AND type = 'recurrente' AND DATE_FORMAT(date_, '%Y-%m') = ?");
$stmt->execute([$_SESSION["id_association"], $moisActuel]);
$nbRecurrentes = $stmt->fetchColumn();

// Nombre de réservations ponctuelles du mois
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservations 
    WHERE id_association = ? AND type = 'ponctuelle' AND DATE_FORMAT(date_, '%Y-%m') = ?");
$stmt->execute([$_SESSION["id_association"], $moisActuel]);
$nbPonctuelles = $stmt->fetchColumn();
$quotaMax = 3;

// Nombre de PDFs
$stmt = $conn->prepare("SELECT COUNT(*) FROM pdfs 
    WHERE id_reservation IN (SELECT id FROM reservations WHERE id_association = ?)");
$stmt->execute([$_SESSION["id_association"]]);
$nbPdfs = $stmt->fetchColumn();

// Mois en français
$moisFr = ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"];
$nomMois = $moisFr[date("n") - 1] . " " . date("Y");

// ===== CARTES RESERVATIONS =====

$stmt = $conn->prepare("SELECT * FROM reservations WHERE id_association = ?");
$stmt->execute([$_SESSION["id_association"]]);
$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$reservation) {
    $reservation = [];
}

$cartesHTML = "";

foreach ($reservation as $r) {

    $stmt = $conn->prepare("SELECT nom FROM salles WHERE id = ?");
    $stmt->execute([$r["id_salle"]]);
    $nomSalle = $stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT * FROM commentaires WHERE id_reservation = ?");
    $stmt->execute([$r["id"]]);
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $messagesHTML = "";
    if (!empty($commentaires)) {
        foreach ($commentaires as $c) {
            $stmt = $conn->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
            $stmt->execute([$c["id_auteur"]]);
            $nomcommentaire = $stmt->fetchColumn();

            $initiales = strtoupper(substr($nomcommentaire ?? "?", 0, 2));
            $messagesHTML .= "
            <div class='message'>
                <div class='avatar'>{$initiales}</div>
                <div class='message-contenu'>
                    <p><span class='auteur'>{$nomcommentaire}</span> <span class='date-message'>{$c['date_creation']}</span></p>
                    <p>{$c['contenu']}</p>
                </div>
            </div>
            ";
        }
    } else {
        $messagesHTML = "<p style='color:#aaa; font-size:13px;'>Aucun message.</p>";
    }

    $badge = match($r["statut"]) {
        "en_attente" => "En attente",
        "validee"    => "Validée",
        "annulee"    => "Annulée",
        default      => "Inconnu"
    };

    $carte = "
<div class='carte-reservation'>
    <button class='entete-bouton' data-cible='panneau-{$r['id']}'>
        <div class='entete-gauche'>
            <div class='barre-couleur'></div>
            <div class='date-heure'>
                <p>{$r['date_']}</p>
                <p>" . substr($r['heure_debut'], 0, 5) . " – " . substr($r['heure_fin'], 0, 5) . "</p>
            </div>
            <div class='titre-salle'>
                <p>{$r['Motif']}</p>
                <p>{$nomSalle}</p>
            </div>
        </div>
        <div class='entete-droite'>
            <span class='statut-badge'>{$badge}</span>
            <span class='chevron'>&#9660;</span>
        </div>
    </button>

    <div class='panneau' id='panneau-{$r['id']}'>
        <div class='panneau-contenu'>
            <div class='boutons-action'>
                <button class='btn-modifier'
                    data-id-reservation='{$r['id']}'
                    data-id-salle='{$r['id_salle']}'
                    data-date='{$r['date_']}'
                    data-heure-debut='" . substr($r['heure_debut'], 0, 5) . "'
                    data-heure-fin='" . substr($r['heure_fin'], 0, 5) . "'>
                    Modifier
                </button>
                <button class='btn-annuler'
                    data-id-reservation='{$r['id']}'>
                    Annuler
                </button>
                <form method='post' action='/ReservationSalle/index.php?page=ajouter_fichier' enctype='multipart/form-data' style='display:inline-block;'>
                    <input type='hidden' name='id_reservation' value='{$r['id']}'>
                    <label class='label-fichier' for='fichier-{$r['id']}'>📎 Ajouter PDF</label>
                    <input type='file' name='fichier' id='fichier-{$r['id']}' class='input-fichier-cache' accept='application/pdf'>
                    <span class='nom-fichier-selectionne' id='nom-fichier-{$r['id']}'></span>
                </form>
            </div>

            <div class='commentaire-box'>Commentaire : {$r['commentaire']}</div>

            <p class='messages-label'>Messages</p>
            {$messagesHTML}

            <div class='ajout-commentaire'>
                <form method='POST' action='/ReservationSalle/index.php?page=responsable'>
                    <input type='hidden' name='id_reservation' value='{$r['id']}'>
                    <input type='text' name='commentaire' placeholder='Ajouter un commentaire... (50 car.)' maxlength='50'>
                    <button type='submit'>Envoyer</button>
                </form>
            </div>
        </div>
    </div>
</div>
";

    $cartesHTML .= $carte;
}

$variables = [
    "{{nomassociation}}"    => $nomAssos,
    "{{carteReservation}}"  => $cartesHTML,
    "{{couleur}}"           => $couleur,
    "{{mois}}"              => $nomMois,
    "{{resadumois}}"        => $nbResaMois,
    "{{nbRecurrentes}}"     => $nbRecurrentes,
    "{{quantité/quantité}}" => $nbPonctuelles . " / " . $quotaMax,
    "{{nbPonctuelles}}"     => $nbPonctuelles,
    "{{quotaMax}}"          => $quotaMax,
    "{{nbPdfs}}"            => $nbPdfs,
];

$template = file_get_contents(__DIR__ . '/../../html/association.html');
$page = str_replace(array_keys($variables), array_values($variables), $template);
echo $page;