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
        u.nom as nom_utilisateur,
        'pdf' AS type_export
    FROM pdfs p
    JOIN reservations r ON p.id_reservation = r.id
    JOIN associations a ON r.id_association = a.id
    JOIN utilisateurs u ON r.id_createur = u.id
    ORDER BY p.date_upload DESC
");
$stmt->execute();
$pdfs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les EXCEL
$stmt = $conn->prepare("
    SELECT 
        e.id,
        e.nom_fichier,
        e.chemin,
        e.date AS date_upload,
        a.nom AS nom_association,
        u.nom AS nom_utilisateur,
        'excel' AS type_export
    FROM excel e
    LEFT JOIN associations a ON e.id_association = a.id
    LEFT JOIN utilisateurs u ON e.id_auteur = u.id
    ORDER BY e.date DESC
");
$stmt->execute();
$excels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les CALENDAR
$stmt = $conn->prepare("
    SELECT 
        c.id,
        c.nom_fichier,
        c.chemin,
        c.date AS date_upload,
        a.nom AS nom_association,
        u.nom AS nom_utilisateur,
        'calendar' AS type_export
    FROM calendar c
    LEFT JOIN associations a ON c.id_association = a.id
    LEFT JOIN utilisateurs u ON c.id_auteur = u.id
    ORDER BY c.date DESC
");
$stmt->execute();
$calendars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fusionner
$exports = array_merge($pdfs, $excels, $calendars);

// Trier par date
usort($exports, function($a, $b) {
    return strtotime($b['date_upload']) - strtotime($a['date_upload']);
});

$nombrePdf = count($exports);

// Générer les lignes
$lignesHistorique = "";

foreach ($exports as $exp) {

    $date = $exp["date_upload"] ? date("d/m/Y H:i", strtotime($exp["date_upload"])) : "-";

    if ($exp["type_export"] === "excel") {
    $icone = "<i class='fa-solid fa-file-excel' style='color:#1D6F42;'></i> Excel";
    $urlTelechargement = "/ProjetReservationSalle/" . $exp['chemin'];

    } elseif ($exp["type_export"] === "calendar") {
        $icone = "<i class='fa-solid fa-calendar' style='color:#0A4CF8;'></i> Calendar";
        $urlTelechargement = "/ProjetReservationSalle/" . $exp['chemin'];

    } else {
        $icone = "<i class='fa-solid fa-file-lines' style='color:green;'></i> PDF";
        $urlTelechargement = "/ProjetReservationSalle/index.php?page=telecharger_pdf&id={$exp['id']}";
    }


    $lignesHistorique .= "
<div class='div_ligne'>
    <p>$icone</p>
    <p>{$date}</p>
    <p>" . ($exp['nom_association'] ?? "Toutes les associations") . "</p>

    <p>{$exp['nom_utilisateur']}</p>
    <p><span class='badge_statut'>Disponible</span></p>

    <p>
        <a href='$urlTelechargement' class='btn_telecharger'>
            <i class='fa-solid fa-download'></i>
        </a>

        <a href='./index.php?page=supprimer_export&type={$exp['type_export']}&id={$exp['id']}' 
           class='btn_supprimer' 
           style='color:red; margin-left:10px;'>
            <i class='fa-solid fa-trash'></i>
        </a>
    </p>
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
