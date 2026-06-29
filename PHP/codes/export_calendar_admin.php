<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: /ReservationSalle/index.php?page=login");
    exit;
}

$periode        = $_POST["periode"] ?? "mois-courant";
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
        SELECT r.*, a.nom as nom_association, s.nom as nom_salle
        FROM reservations r
        JOIN associations a ON r.id_association = a.id
        JOIN salles s ON r.id_salle = s.id
        WHERE r.date_ BETWEEN ? AND ?
        ORDER BY r.date_
    ");
    $stmt->execute([$debut, $fin]);
} else {
    $stmt = $conn->prepare("
        SELECT r.*, a.nom as nom_association, s.nom as nom_salle
        FROM reservations r
        JOIN associations a ON r.id_association = a.id
        JOIN salles s ON r.id_salle = s.id
        WHERE r.date_ BETWEEN ? AND ? AND r.id_association = ?
        ORDER BY r.date_
    ");
    $stmt->execute([$debut, $fin, $id_association]);
}

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="reservations_' . $debut . '_' . $fin . '.ics"');

echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:-//ReservationSalle//FR\r\n";

foreach ($reservations as $r) {
    $dateDebut = str_replace('-', '', $r['date_']) . 'T' . str_replace(':', '', substr($r['heure_debut'], 0, 5)) . '00';
    $dateFin   = str_replace('-', '', $r['date_']) . 'T' . str_replace(':', '', substr($r['heure_fin'], 0, 5)) . '00';

    echo "BEGIN:VEVENT\r\n";
    echo "DTSTART:" . $dateDebut . "\r\n";
    echo "DTEND:" . $dateFin . "\r\n";
    echo "SUMMARY:" . $r['Motif'] . " - " . $r['nom_association'] . "\r\n";
    echo "LOCATION:" . $r['nom_salle'] . "\r\n";
    echo "END:VEVENT\r\n";
}

echo "END:VCALENDAR\r\n";
exit;