<?php
require_once(__DIR__ . '/connexionBDD.php');

// Récupérer les associations depuis la BDD
$stmt = $conn->prepare("SELECT id, nom FROM associations ORDER BY nom");
$stmt->execute();
$associations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$optionsAssociations = "<option value='0'>Toutes les associations</option>";
foreach ($associations as $a) {
    $optionsAssociations .= "<option value='{$a['id']}'>{$a['nom']}</option>";
}

// Récupérer les PDFs pour l'historique
$stmt = $conn->prepare("
    SELECT 
        p.id,
        p.nom_fichier,
        p.chemin,
        p.date_upload,
        a.nom as nom_association,
        u.nom as nom_utilisateur
    FROM pdfs p
    JOIN reservations r ON p.id_reservation = r.id
    JOIN associations a ON r.id_association = a.id
    JOIN utilisateurs u ON r.id_createur = u.id
    ORDER BY p.date_upload DESC
");
$stmt->execute();
$pdfs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nombrePdf = count($pdfs);

// Générer les lignes de l'historique
$lignesHistorique = "";
foreach ($pdfs as $p) {
    $date = $p["date_upload"] ? date("d/m/Y H:i", strtotime($p["date_upload"])) : "-";
    $lignesHistorique .= "
    <div class='div_ligne'>
        <p><i class='fa-solid fa-file-lines' style='color:green;'></i> PDF</p>
        <p>{$date}</p>
        <p>{$p['nom_association']}</p>
        <p>{$p['nom_utilisateur']}</p>
        <p><span class='badge_statut'>Disponible</span></p>
        <p><a href='/ReservationSalle/index.php?page=telecharger_pdf&id={$p['id']}' class='btn_telecharger'>
            <i class='fa-solid fa-download'></i>
        </a></p>
    </div>
    ";
}

if ($lignesHistorique === "") {
    $lignesHistorique = "<div class='div_ligne'><p colspan='6' style='color:#9ca3af;'>Aucun export disponible.</p></div>";
}

$variables = [
    "{{optionsAssociations}}" => $optionsAssociations,
    "{{nombrePdf}}"           => $nombrePdf,
    "{{lignesHistorique}}"    => $lignesHistorique
];

$template = file_get_contents(__DIR__ . '/../../HTML/exportations.html');
$page = str_replace(array_keys($variables), array_values($variables), $template);
echo $page;