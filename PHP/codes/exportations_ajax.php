<?php
require_once(__DIR__ . '/connexionBDD.php');

$id = $_POST['id'] ?? null;
$type = $_POST['type'] ?? null;

if (!$id || !$type) {
    echo "";
    exit;
}

if ($type === "excel") {

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
        WHERE e.id = ?
    ");

} elseif ($type === "calendar") {

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
        WHERE c.id = ?
    ");

} else { // PDF

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
        WHERE p.id = ?
    ");
}

$stmt->execute([$id]);
$exp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$exp) {
    echo "";
    exit;
}

$date = date("d/m/Y H:i", strtotime($exp["date_upload"]));
$nomAssociation = $exp["nom_association"] ?? "Toutes les associations";

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

echo "
<div class='div_ligne'>
    <p>$icone</p>
    <p>{$date}</p>
    <p>{$nomAssociation}</p>
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
