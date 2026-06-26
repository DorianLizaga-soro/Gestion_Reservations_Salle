<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: /ReservationSalle/index.php?page=login");
    exit;
}

$periode = $_POST["periode"] ?? "mois-courant";

// Définir la période
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
    default: // mois-courant
        $debut = date("Y-m-01");
        $fin   = date("Y-m-t");
        break;
}

$stmt = $conn->prepare("SELECT r.*, s.nom as nom_salle 
    FROM reservations r 
    JOIN salles s ON r.id_salle = s.id
    WHERE r.id_association = ? AND r.date_ BETWEEN ? AND ?
    ORDER BY r.date_");
$stmt->execute([$_SESSION["id_association"], $debut, $fin]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Générer le CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="reservations.csv"');

$output = fopen('php://output', 'w');

// BOM pour Excel (pour les accents)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// En-têtes
fputcsv($output, ['Date', 'Heure début', 'Heure fin', 'Salle', 'Motif', 'Statut'], ';');

// Données
foreach ($reservations as $r) {
    fputcsv($output, [
        $r['date_'],
        substr($r['heure_debut'], 0, 5),
        substr($r['heure_fin'], 0, 5),
        $r['nom_salle'],
        $r['Motif'],
        $r['statut']
    ], ';');
}

fclose($output);
exit;