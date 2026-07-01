<?php
require_once(__DIR__ . '/connexionBDD.php');


$idUtilisateur = $_SESSION["id"];
$idAssociation = $_SESSION["id_association"] ?? null;
$role = $_SESSION["select_role"];

// Récupérer la réservation active (via GET)
$idReservationActive = $_GET["reservation"] ?? null;

// Récupérer les conversations (réservations avec messages)
if ($role === "gestionnaire") {
    $stmt = $conn->prepare("
        SELECT r.id, r.Motif, r.date_, r.statut, r.id_association,
               a.nom as nom_association, a.couleur,
               s.nom as nom_salle,
               COUNT(m.id) as nb_messages,
               SUM(CASE WHEN m.lu = 0 AND m.id_auteur != ? THEN 1 ELSE 0 END) as nb_non_lus,
               MAX(m.contenu) as dernier_message
        FROM reservations r
        JOIN associations a ON r.id_association = a.id
        JOIN salles s ON r.id_salle = s.id
        LEFT JOIN messages m ON m.id_reservation = r.id
        GROUP BY r.id
        ORDER BY MAX(m.date_envoi) DESC
    ");
    $stmt->execute([$idUtilisateur]);
} else {
    $stmt = $conn->prepare("
        SELECT r.id, r.Motif, r.date_, r.statut, r.id_association,
               a.nom as nom_association, a.couleur,
               s.nom as nom_salle,
               COUNT(m.id) as nb_messages,
               SUM(CASE WHEN m.lu = 0 AND m.id_auteur != ? THEN 1 ELSE 0 END) as nb_non_lus,
               MAX(m.contenu) as dernier_message
        FROM reservations r
        JOIN associations a ON r.id_association = a.id
        JOIN salles s ON r.id_salle = s.id
        LEFT JOIN messages m ON m.id_reservation = r.id
        WHERE r.id_association = ?
        GROUP BY r.id
        ORDER BY MAX(m.date_envoi) DESC
    ");
    $stmt->execute([$idUtilisateur, $idAssociation]);
}

$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Compter total non lus
$nbNonLus = 0;
foreach ($conversations as $c) {
    $nbNonLus += $c["nb_non_lus"];
}

// Générer la liste des conversations
$listeConversations = "";
foreach ($conversations as $c) {
    $couleur = $c["couleur"];
    $initiales = strtoupper(substr($c["nom_association"], 0, 2));
    $actif = ($idReservationActive == $c["id"]) ? "actif" : "";
    $badgeHTML = $c["nb_non_lus"] > 0 ? "<span class='conv-badge'>{$c['nb_non_lus']}</span>" : "";

    $listeConversations .= "
   
        <div class='conversation-item {$actif}' data-id='{$c['id']}'>
            <div class='conv-avatar' style='background: {$couleur};'>{$initiales}</div>
            <div class='conv-infos'>
                <p class='conv-nom'>{$c['nom_association']}</p>
                <p class='conv-motif'>{$c['Motif']}</p>
                <p class='conv-dernier-message'>{$c['dernier_message']}</p>
            </div>
            {$badgeHTML}
        </div>
    
    ";
}

if ($listeConversations === "") {
    $listeConversations = "<p style='padding:20px; color:#9ca3af; font-size:13px;'>Aucune conversation.</p>";
}

// Générer l'en-tête et les messages de la conversation active
$enteteConversation = "";
$messagesHTML = "";

if ($idReservationActive) {

    // Marquer les messages comme lus
    $stmt = $conn->prepare("UPDATE messages SET lu = 1 WHERE id_reservation = ? AND id_auteur != ?");
    $stmt->execute([$idReservationActive, $idUtilisateur]);

    // Infos de la réservation
    $stmt = $conn->prepare("
        SELECT r.*, a.nom as nom_association, a.couleur, s.nom as nom_salle
        FROM reservations r
        JOIN associations a ON r.id_association = a.id
        JOIN salles s ON r.id_salle = s.id
        WHERE r.id = ?
    ");
    $stmt->execute([$idReservationActive]);
    $resa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resa) {
        $couleur = $resa["couleur"];
        $initiales = strtoupper(substr($resa["nom_association"], 0, 2));
        $badgeStatut = $resa["statut"];
        $dateFormatee = date("d/m/Y", strtotime($resa["date_"]));

        $enteteConversation = "
        <div class='entete-gauche'>
            <div class='entete-avatar' style='background: {$couleur};'>{$initiales}</div>
            <div>
                <p class='entete-nom'>{$resa['nom_association']}</p>
                <p class='entete-infos'>
                    <i class='fa-solid fa-calendar-days'></i>
                    {$resa['Motif']} · {$resa['nom_salle']} · {$dateFormatee}
                </p>
            </div>
        </div>
        <span class='badge-statut {$badgeStatut}'>{$badgeStatut}</span>
        ";

        // Récupérer les messages
        $stmt = $conn->prepare("
            SELECT m.*, u.nom as nom_auteur, u.role as role_auteur
            FROM messages m
            JOIN utilisateurs u ON m.id_auteur = u.id
            WHERE m.id_reservation = ?
            ORDER BY m.date_envoi ASC
        ");
        $stmt->execute([$idReservationActive]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dateCourante = "";

        foreach ($messages as $msg) {
            $dateMsg = date("d/m/Y", strtotime($msg["date_envoi"]));
            $heureMsg = date("H:i", strtotime($msg["date_envoi"]));
            $estMoi = ($msg["id_auteur"] == $idUtilisateur);
            $classMoi = $estMoi ? "moi" : "";
            $initMot = strtoupper(substr($msg["nom_auteur"], 0, 2));
            $avatarCouleur = $estMoi ? "#2563eb" : "#6b7280";
            $statut = $msg["lu"] ? "✓✓ Lu" : "✓ Envoyé";
            $roleLabel = match($msg["role_auteur"]) {
                "responsable_association" => "Responsable",
                "gestionnaire" => "Gestionnaire",
                "personnel_menage" => "Personnel",
                "membre_association" => "Membre",
                default => ""
            };

            // Séparateur de date
            if ($dateMsg !== $dateCourante) {
                $dateCourante = $dateMsg;
                $messagesHTML .= "<div class='separateur-date'>{$dateMsg}</div>";
            }

            $messagesHTML .= "
            <div class='message-bloc {$classMoi}'>
                <div class='message-avatar' style='background: {$avatarCouleur};'>{$initMot}</div>
                <div class='message-contenu'>
                    <div class='message-meta'>
                        <span class='message-auteur'>{$msg['nom_auteur']}</span>
                        <span class='message-heure'>{$heureMsg}</span>
                        <span class='badge-role {$msg['role_auteur']}'>{$roleLabel}</span>
                    </div>
                    <div class='message-bulle'>{$msg['contenu']}</div>
                    <div class='message-statut'>{$statut}</div>
                </div>
            </div>
            ";
        }

        if ($messagesHTML === "") {
            $messagesHTML = "<div class='messages-vide'><i class='fa-regular fa-comment'></i><p>Aucun message pour cette réservation.</p></div>";
        }
    }

} else {
    $enteteConversation = "";
    $messagesHTML = "<div class='messages-vide'><i class='fa-regular fa-comment-dots'></i><p>Sélectionnez une conversation.</p></div>";
}

$variables = [
    "{{nbNonLus}}"           => $nbNonLus,
    "{{listeConversations}}" => $listeConversations,
    "{{enteteConversation}}" => $enteteConversation,
    "{{messagesHTML}}"       => $messagesHTML,
    "{{idReservationActive}}" => $idReservationActive ?? ""
];


$template = file_get_contents(__DIR__ . '/../../HTML/messagerie.html');
$page = str_replace(array_keys($variables), array_values($variables), $template);
echo $page;