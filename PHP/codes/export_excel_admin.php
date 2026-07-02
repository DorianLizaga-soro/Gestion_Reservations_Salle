<?php
require_once(__DIR__ . '/connexionBDD.php');

$periode      = $_POST["periode"] ?? "mois-courant";
$id_association = $_POST["id_association"] ?? 0;

// Calcul période
switch($periode) {
    case "mois-precedent":
        $debut = date("Y-m-01", strtotime("-1 month"));
        $fin   = date("Y-m-t", strtotime("-1 month"));
        break;
    case "trimestre":
        $debut = date("Y-m-01", strtotime("-3 month"));
        $fin   = date("Y-m-t");
        break;
    case "annee":
        $debut = date("Y-01-01");
        $fin   = date("Y-12-31");
        break;
    default:
        $debut = date("Y-m-01");
        $fin   = date("Y-m-t");
        break;
}

// Récupération des réservations
if ($id_association == 0) {
    $stmt = $conn->prepare("
        SELECT r.date_, r.heure_debut, r.heure_fin, r.Motif, r.statut, r.type,
               a.nom as nom_association, s.nom as nom_salle,
               u.nom as nom_responsable
        FROM reservations r
        JOIN associations a ON r.id_association = a.id
        JOIN salles s ON r.id_salle = s.id
        JOIN utilisateurs u ON r.id_createur = u.id
        WHERE r.date_ BETWEEN ? AND ?
        ORDER BY r.date_
    ");
    $stmt->execute([$debut, $fin]);
} else {
    $stmt = $conn->prepare("
        SELECT r.date_, r.heure_debut, r.heure_fin, r.Motif, r.statut, r.type,
               a.nom as nom_association, s.nom as nom_salle,
               u.nom as nom_responsable
        FROM reservations r
        JOIN associations a ON r.id_association = a.id
        JOIN salles s ON r.id_salle = s.id
        JOIN utilisateurs u ON r.id_createur = u.id
        WHERE r.date_ BETWEEN ? AND ? AND r.id_association = ?
        ORDER BY r.date_
    ");
    $stmt->execute([$debut, $fin, $id_association]);
}

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔥 1) Générer le fichier sur le serveur
$nomFichier = "export_" . date("Ymd_His") . ".csv";

// IMPORTANT : remonter d’un dossier pour atteindre /uploads/
$cheminServeur = "./uploads/exports/" . $nomFichier;

if (!is_dir("./uploads/exports")) {
    mkdir("./uploads/exports", 0755, true);
}

$output = fopen($cheminServeur, 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['Association', 'Salle', 'Date', 'Heure début', 'Heure fin', 'Motif', 'Type', 'Statut', 'Responsable'], ';');

foreach ($reservations as $r) {
    fputcsv($output, [
        $r['nom_association'],
        $r['nom_salle'],
        $r['date_'],
        substr($r['heure_debut'], 0, 5),
        substr($r['heure_fin'], 0, 5),
        $r['Motif'],
        $r['type'],
        $r['statut'],
        $r['nom_responsable']
    ], ';');
}

fclose($output);

// 🔥 2) Enregistrer dans la table excel
$id_association_insert = ($id_association == 0) ? null : $id_association;

$stmt = $conn->prepare("
    INSERT INTO excel (date, id_auteur, id_association, nom_fichier, chemin)
    VALUES (NOW(), ?, ?, ?, ?)
");

$stmt->execute([
    $_SESSION['id'],
    $id_association_insert,
    $nomFichier,
    "uploads/exports/" . $nomFichier
]);



// 🔥 3) Télécharger automatiquement
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $nomFichier . '"');
header('Content-Length: ' . filesize($cheminServeur));

readfile($cheminServeur);
exit;
