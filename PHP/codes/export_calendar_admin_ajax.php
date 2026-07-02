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
        SELECT r.*, a.nom AS nom_association, s.nom AS nom_salle
        FROM reservations r
        JOIN associations a ON r.id_association = a.id
        JOIN salles s ON r.id_salle = s.id
        WHERE r.date_ BETWEEN ? AND ?
    ");
    $stmt->execute([$debut, $fin]);
} else {
    $stmt = $conn->prepare("
        SELECT r.*, a.nom AS nom_association, s.nom AS nom_salle
        FROM reservations r
        JOIN associations a ON r.id_association = a.id
        JOIN salles s ON r.id_salle = s.id
        WHERE r.date_ BETWEEN ? AND ? AND r.id_association = ?
    ");
    $stmt->execute([$debut, $fin, $id_association]);
}

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 1) Générer le fichier ICS
$nomFichier = "calendar_" . date("Ymd_His") . ".ics";
$cheminServeur = "./uploads/exports/" . $nomFichier;

if (!is_dir("./uploads/exports")) {
    mkdir("./uploads/exports", 0755, true);
}

$ics = "BEGIN:VCALENDAR\nVERSION:2.0\nCALSCALE:GREGORIAN\n";

foreach ($reservations as $r) {
    $start = date("Ymd\THis", strtotime($r["date_"] . " " . $r["heure_debut"]));
    $end   = date("Ymd\THis", strtotime($r["date_"] . " " . $r["heure_fin"]));

    $ics .= "BEGIN:VEVENT\n";
    $ics .= "SUMMARY:" . $r["Motif"] . "\n";
    $ics .= "DTSTART:" . $start . "\n";
    $ics .= "DTEND:" . $end . "\n";
    $ics .= "LOCATION:" . $r["nom_salle"] . "\n";
    $ics .= "END:VEVENT\n";
}

$ics .= "END:VCALENDAR";

file_put_contents($cheminServeur, $ics);

// 2) Enregistrer dans la table calendar
$id_association_insert = ($id_association == 0) ? null : $id_association;

$stmt = $conn->prepare("
    INSERT INTO calendar (date, id_auteur, id_association, nom_fichier, chemin)
    VALUES (NOW(), ?, ?, ?, ?)
");

$stmt->execute([
    $_SESSION['id'],
    $id_association_insert,
    $nomFichier,
    "uploads/exports/" . $nomFichier
]);

$idExportCree = $conn->lastInsertId();

// 3) Retour JSON pour AJAX
echo json_encode([
    "id"     => $idExportCree,
    "chemin" => "/ProjetReservationSalle/uploads/exports/" . $nomFichier
]);
exit;
