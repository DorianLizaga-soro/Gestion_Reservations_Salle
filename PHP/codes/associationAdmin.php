<?php
require_once(__DIR__ . '/connexionBDD.php');


function getTrimestre($date){

 $month = (int)date("m", strtotime($date));
 return ceil($month / 3);
}

$trimestreActuel = getTrimestre(date("Y-m-d"));

// Récupérer toutes les associations avec leurs stats
$stmt = $conn->prepare("
    SELECT 
        a.id,
        a.nom,
        a.couleur,
        a.id_responsable,
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
$cartesModal ="";

foreach ($associations as $a) {

$quotaMax = 3;

// Récupérer le nom du responsable actuel
$stmtR = $conn->prepare("SELECT nom FROM utilisateurs WHERE id = ?");
$stmtR->execute([$a["id_responsable"]]);
$nomResponsable = $stmtR->fetchColumn() ?? "Aucun";

    
    //historique des reservations assos 
    $stmtH = $conn->prepare("
    SELECT Motif,id_salle, date_, heure_debut, heure_fin, type 
    FROM reservations 
    WHERE id_association = ?
    ORDER BY date_ DESC
");
$stmtH->execute([$a["id"]]);
$historique = $stmtH->fetchAll(PDO::FETCH_ASSOC);

$stmtRes = $conn->prepare("
    SELECT id 
    FROM reservations 
    WHERE id_association = ?
");
$stmtRes->execute([$a["id"]]);
$resIds = $stmtRes->fetchAll(PDO::FETCH_COLUMN);

// Si aucune réservation → aucun document
if (empty($resIds)) {
    $documentHTML = "<p>Aucun document.</p>";
} else {

    // Récupérer les PDF liés aux réservations
    $in = implode(",", $resIds);

    $stmtDocs = $conn->prepare("
        SELECT nom_fichier, date_upload, chemin
        FROM pdfs
        WHERE id_reservation IN ($in)
        ORDER BY date_upload DESC
    ");
    $stmtDocs->execute();
    $documents = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

    $documentHTML = "";

foreach ($documents as $d) {
    $documentHTML .= "
        <div class='historique-ligne' style='border-left: 3px solid {$couleur};'>
            <div>
                <p class='historique-titre'>{$d["nom_fichier"]}</p>
                <p class='historique-sous-titre'>Déposé le {$d["date_upload"]}</p>
            </div>
            <div class='historique-droite'>
                <a href='{$d["chemin"]}' target='_blank' class='btn-doc'>Ouvrir</a>
            </div>
        </div>
    ";
}

if ($documentHTML === "") {
    $documentHTML = "<p>Aucun document.</p>";
}
}



    // Compter les réservations ponctuelles du mois
    $stmt2 = $conn->prepare("SELECT COUNT(*) FROM reservations 
        WHERE id_association = ? AND type = 'ponctuelle' 
        AND DATE_FORMAT(date_, '%Y-%m') = ?");
    $stmt2->execute([$a["id"], date("Y-m")]);
    $nbPonctuelles = $stmt2->fetchColumn();
    
    $pourcent = min(($nbPonctuelles / $quotaMax) * 100, 100);
    $couleur = $a["couleur"];

    // 1. Récupérer les membres de l'association
    $stmtM = $conn->prepare("SELECT nom, role FROM utilisateurs WHERE id_association = ?");
    $stmtM->execute([$a["id"]]);
    $membres = $stmtM->fetchAll(PDO::FETCH_ASSOC);

    // 2. Construire le HTML des membres
    $membresHTML = "";
    foreach ($membres as $m) {
        $initiale = strtoupper($m["nom"][0]);

        $membresHTML .= "
            <div class='membre-ligne'>
                <div class='membre-avatar'>{$initiale}</div>
                <div>
                    <p class='membre-nom'>{$m["nom"]}</p>
                    <p class='membre-role'>{$m["role"]}</p>
                </div>
            </div>
        ";
    }

    $historiqueHTML = "";

foreach ($historique as $h) {

    $date = date("d/m/Y", strtotime($h["date_"]));
    $badge = $h["type"] === "recurrente" 
        ? "<span class='badge-recurrente'>Récurrente</span>"
        : "<span class='badge-ponctuelle'>Ponctuelle</span>";

    $historiqueHTML .= "
        <div class='historique-ligne' style='border-left: 3px solid {$couleur};'>
            <div>
                <p class='historique-titre'>{$h["Motif"]}</p>
                <p class='historique-sous-titre'> {$h["heure_debut"]}–{$h["heure_fin"]}</p>
            </div>
            <div class='historique-droite'>
                <p class='historique-date'>{$date}</p>
                {$badge}
            </div>
        </div>
    ";
}
if ($historiqueHTML === "") {
    $historiqueHTML = "<p>Aucune réservation passée.</p>";
}





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
            data-id='{$a['id']}'
                data-nom='{$a['nom']}'
                data-couleur='{$couleur}'
                data-membres='{$a['nb_membres']}'
                data-reservations='{$a['nb_reservations']}'
                style='background: {$couleur}22; color: {$couleur};'>
                <i class='fa-solid fa-eye'></i> Voir détails
            </button>
           <button class='btn-modifier-asso' 
    data-id='{$a['id']}' 
    data-nom='{$a['nom']}' 
    data-couleur='{$couleur}'
    data-responsable='{$nomResponsable}'>
    <i class='fa-solid fa-pen'></i>
</button>
            <button class='btn-supprimer-asso' data-id='{$a['id']}' data-nom='{$a['nom']}'>
                <i class='fa-solid fa-trash'></i>
            </button>
        </div>
    </div>
    ";

$cartesModal .= "
 

<div class='overlay-details' id='overlay-details-{$a['id']}'>
    <div class='modale-details'>

        <button class='fermer-details' data-id='{$a['id']}'>&times;</button>

        <div class='modale-details-entete'>
            <div class='modale-details-icone' style='background: {$couleur}22;'>
                <i class='fa-solid fa-building-columns' style='color: {$couleur};'></i>
            </div>
            <div>
                <p class='modale-details-nom'>{$a['nom']}</p>
                <p class='modale-details-stats'>
                    <span><i class='fa-solid fa-user-group'></i> {$a['nb_membres']} membres</span>
                    <span><i class='fa-solid fa-calendar-days'></i> {$a['nb_reservations']} réservations</span>
                </p>
            </div>
           <div class='modale-details-onglets'>
    <button class='onglet-btn actif' data-onglet='info-{$a['id']}'>Informations</button>
    <button class='onglet-btn' data-onglet='cal-{$a['id']}'>Calendrier</button>
    <button class='onglet-btn' data-onglet='histo-{$a['id']}'>Historique</button>
    <button class='onglet-btn' data-onglet='docs-{$a['id']}'>Documents</button>
</div>

        </div>

     <div class='modale-details-corps'>

    <div class='onglet-panel actif' id='panel-info-{$a['id']}'>
        <div class='details-box'>
            <p class='details-box-titre'>Membres principaux</p>
            {$membresHTML}
        </div>
    </div>

    <div class='onglet-panel' id='panel-cal-{$a['id']}'>
        <div class='details-calendrier-vide'>
            <i class='fa-regular fa-calendar'></i>
            <p>{$a['nb_reservations']} réservations ce mois</p>
        </div>
    </div>

    <div class='onglet-panel' id='panel-histo-{$a['id']}'>
        {$historiqueHTML}
    </div>

    <div class='onglet-panel' id='panel-docs-{$a['id']}'>
        {$documentHTML}
    </div>

</div>



    </div>
</div>

";


}

// Récupérer les utilisateurs responsables
$stmt = $conn->prepare("SELECT id, nom FROM utilisateurs WHERE role = 'responsable_association'");
$stmt->execute();
$responsables = $stmt->fetchAll(PDO::FETCH_ASSOC);

$optionsResponsables = "";
foreach ($responsables as $r) {
    $optionsResponsables .= "<option value='{$r['id']}'>{$r['nom']}</option>";
}


   
$variables = [
    "{{nbAssociations}}" => $nbAssociations,
    "{{cartesAssociations}}" => $cartesHTML,
    "{{cartesModal}}"=>$cartesModal,
    "{{annee}}"=>date("Y"),
    "{{optionsResponsables}}" => $optionsResponsables
];

$template = file_get_contents(__DIR__ . '/../../html/associationAdmin.html');
$page = str_replace(array_keys($variables), array_values($variables), $template);
echo $page;