<?php
require_once(__DIR__ . '/connexionBDD.php');

$idReservation = $_GET["reservation"] ?? null;

if (!$idReservation) {
    echo json_encode([
        "entete" => "",
        "messages" => "<div class='messages-vide'><p>Aucune conversation.</p></div>",
        "footer" => ""
    ]);
    exit;
}

// Marquer les messages comme lus quand la conversation est ouverte
$stmt = $conn->prepare("
    UPDATE messages 
    SET lu = 1 
    WHERE id_reservation = ? 
    AND id_auteur != ?
");
$stmt->execute([$idReservation, $_SESSION["id"]]);

// ===== ENTÊTE =====
$stmt = $conn->prepare("
    SELECT r.*, a.nom AS nom_association, a.couleur, s.nom AS nom_salle
    FROM reservations r
    JOIN associations a ON r.id_association = a.id
    JOIN salles s ON r.id_salle = s.id
    WHERE r.id = ?
");
$stmt->execute([$idReservation]);
$resa = $stmt->fetch(PDO::FETCH_ASSOC);

$entete = "
<div class='entete-gauche'>
    <div class='entete-avatar' style='background: {$resa['couleur']};'>
        " . strtoupper(substr($resa["nom_association"], 0, 2)) . "
    </div>
    <div>
        <p class='entete-nom'>{$resa['nom_association']}</p>
        <p class='entete-infos'>
            <i class='fa-solid fa-calendar-days'></i>
            {$resa['Motif']} · {$resa['nom_salle']} · " . date("d/m/Y", strtotime($resa["date_"])) . "
        </p>
    </div>
</div>
<span class='badge-statut {$resa['statut']}'>{$resa['statut']}</span>
";

// ===== MESSAGES =====
$stmt = $conn->prepare("
    SELECT m.*, u.nom AS nom_auteur, u.role AS role_auteur
    FROM messages m
    JOIN utilisateurs u ON m.id_auteur = u.id
    WHERE m.id_reservation = ?
    ORDER BY m.date_envoi ASC
");
$stmt->execute([$idReservation]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$messagesHTML = "";

if (!$messages) {
    $messagesHTML .= "<div class='messages-vide'><p>Aucun message.</p></div>";
} else {
    $dateCourante = "";

    foreach ($messages as $msg) {
        $dateMsg = date("d/m/Y", strtotime($msg["date_envoi"]));
        $heureMsg = date("H:i", strtotime($msg["date_envoi"]));
        $estMoi = ($msg["id_auteur"] == $_SESSION["id"]);
        $classMoi = $estMoi ? "moi" : "";
        $init = strtoupper(substr($msg["nom_auteur"], 0, 2));
        $avatarCouleur = $estMoi ? "#2563eb" : "#6b7280";

        if ($dateMsg !== $dateCourante) {
            $dateCourante = $dateMsg;
            $messagesHTML .= "<div class='separateur-date'>{$dateMsg}</div>";
        }

        $messagesHTML .= "
        <div class='message-bloc {$classMoi}'>
            <div class='message-avatar' style='background: {$avatarCouleur};'>{$init}</div>
            <div class='message-contenu'>
                <div class='message-meta'>
                    <span>{$msg['nom_auteur']}</span>
                    <span>{$heureMsg}</span>
                </div>
                <div class='message-bulle'>{$msg['contenu']}</div>
            </div>
        </div>";
    }
}


// ===== FOOTER =====
$footer = "
<div class='messages-footer' id='messages-footer'>
    <form method='POST' action='./index.php?page=envoyer_message' id='form-message'>
        <input type='hidden' name='id_reservation' value='{$idReservation}'>
        <input type='text' name='contenu' id='input-message'
            placeholder='Écrire un message... (50 caractères max)' maxlength='50' autocomplete='off'>
        <span class='compteur'>0/50</span>
        <button type='submit' class='btn-envoyer'>
            <i class='fa-solid fa-paper-plane'></i>
        </button>
    </form>
</div>
";


echo json_encode([
    "entete" => $entete,
    "messages" => $messagesHTML,
    "footer" => $footer
]);
