<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: /ReservationSalle/index.php?page=login");
    exit;
}

$id_reservation = $_POST["id_reservation"];
$id_salle       = $_POST["id_salle"];
$date_          = $_POST["date_"];
$heure_debut    = $_POST["heure_debut"];
$heure_fin      = $_POST["heure_fin"];
$motif          = $_POST["Motif"] ?? "";
$commentaire    = $_POST["commentaire"] ?? "";

$stmt = $conn->prepare("UPDATE reservations 
    SET id_salle = ?, date_ = ?, heure_debut = ?, heure_fin = ?, Motif = ?, commentaire = ?
    WHERE id = ?");

$stmt->execute([$id_salle, $date_, $heure_debut, $heure_fin, $motif, $commentaire, $id_reservation]);

header("Location: /ReservationSalle/index.php?page=responsable");
exit;