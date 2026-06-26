<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: /ReservationSalle/index.php?page=login");
    exit;
}

// Vérifier que c'est bien un gestionnaire
if ($_SESSION["select_role"] !== "gestionnaire") {
    header("Location: /ReservationSalle/index.php?page=login");
    exit;
}

// Récupérer toutes les associations avec leurs stats
$stmt = $conn->prepare("
    SELECT 
        a.id,
        a.nom,
        a.couleur,
        COUNT(DISTINCT u.id) as nb_membres,
        COUNT(DISTINCT r.id) as nb_reservations
    FROM associations a
    LEFT JOIN utilisateurs u ON u.id_association = a.id
    LEFT JOIN reservations r ON r.id_association = a.id 
        AND DATE_FORMAT(r.date_, '%Y-%m') = ?
    GROUP BY a.id
    ORDER BY a.nom
");
$stmt->execute([date("Y-m")]);
$associations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nbAssociations = count($associations);

// Générer les cartes HTML
$cartesHTML = "";

foreach ($associations as $a) {
    $quotaMax = 3;
    
    // Compter les réservations ponctuelles du mois
    $stmt2 = $conn->prepare("SELECT COUNT(*) FROM reservations 
        WHERE id_association = ? AND type = 'ponctuelle' 
        AND DATE_FORMAT(date_, '%Y-%m') = ?");
    $stmt2->execute([$a["id"], date("Y-m")]);
    $nbPonctuelles = $stmt2->fetchColumn();
    
    $pourcent = min(($nbPonctuelles / $quotaMax) * 100, 100);
    $couleur = $a["couleur"];
    
    $cartesHTML .= "
    <div class='carte-asso' style='--couleur-asso: {$couleur};'>
        <div class='carte-asso-entete'>
            <div class='carte-asso-icone' style='background: {$couleur}22;'>
                <i class='fa-solid fa-building-columns' style='color: {$couleur};'></i>
            </div>
            <div>
                <p class='carte-asso-nom'>{$a['nom']}</p>
                <p class='carte-asso-couleur'>
                    <span class='pastille-couleur' style='background: {$couleur};'></span>{$couleur}
                </p>
            </div>
        </div>

        <div class='carte-asso-stats'>
            <div class='stat-box'>
                <p class='stat-label'><i class='fa-solid fa-user-group'></i> Membres</p>
                <p class='stat-valeur'>{$a['nb_membres']}</p>
            </div>
            <div class='stat-box'>
                <p class='stat-label'><i class='fa-solid fa-calendar-days'></i> Réservations</p>
                <p class='stat-valeur' style='color: {$couleur};'>{$a['nb_reservations']}</p>
            </div>
        </div>

        <div class='carte-asso-quota'>
            <div class='quota-texte'>
                <span>Quota mensuel (3 ponctuelles)</span>
                <span class='quota-ratio' style='color: {$couleur};'>{$nbPonctuelles}/{$quotaMax}</span>
            </div>
            <div class='quota-barre-fond'>
                <div class='quota-barre-remplie' style='background: {$couleur}; width: {$pourcent}%;'></div>
            </div>
        </div>

        <div class='carte-asso-actions'>
            <button class='btn-voir-details' 
                data-nom='{$a['nom']}'
                data-couleur='{$couleur}'
                data-membres='{$a['nb_membres']}'
                data-reservations='{$a['nb_reservations']}'
                style='background: {$couleur}22; color: {$couleur};'>
                <i class='fa-solid fa-eye'></i> Voir détails
            </button>
            <button class='btn-modifier-asso' data-id='{$a['id']}' data-nom='{$a['nom']}' data-couleur='{$couleur}'>
                <i class='fa-solid fa-pen'></i>
            </button>
            <button class='btn-supprimer-asso' data-id='{$a['id']}' data-nom='{$a['nom']}'>
                <i class='fa-solid fa-trash'></i>
            </button>
        </div>
    </div>
    ";
}

$variables = [
    "{{nbAssociations}}" => $nbAssociations,
    "{{cartesAssociations}}" => $cartesHTML
];

$template = file_get_contents(__DIR__ . '/../../html/associationAdmin.html');
$page = str_replace(array_keys($variables), array_values($variables), $template);
echo $page;