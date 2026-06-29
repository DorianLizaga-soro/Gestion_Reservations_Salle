<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: /ReservationSalle/index.php?page=login");
    exit;
}

$periode      = $_POST["periode"] ?? "mois-courant";
$id_association = $_POST["id_association"] ?? 0;

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

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="reservations_' . $debut . '_' . $fin . '.csv"');

$output = fopen('php://output', 'w');
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
exit;