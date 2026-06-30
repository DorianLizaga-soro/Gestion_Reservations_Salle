<?php

require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: ./index.php?page=login");
    exit;
}

$id_association = $_SESSION["id_association"];
$id_createur    = $_SESSION["id"];
$id_salle       = $_POST["id_salle"];
$date_          = $_POST["date_"];
$heure_debut    = $_POST["heure_debut"];
$heure_fin      = $_POST["heure_fin"];
$motif          = $_POST["Motif"] ?? "";
$commentaire    = $_POST["commentaire"] ?? "";
$id_menage = $_POST["id_menage"];


// 1) INSERT dans reservations
$stmt = $conn->prepare("
    INSERT INTO reservations 
    (id_association, id_salle, type, date_, heure_debut, heure_fin, statut, id_createur, Motif, commentaire) 
    VALUES (?, ?, 'ponctuelle', ?, ?, ?, 'en_attente', ?, ?, ?)
");

$stmt->execute([
    $id_association,
    $id_salle,
    $date_,
    $heure_debut,
    $heure_fin,
    $id_createur,
    $motif,
    $commentaire
]);

// 2) Récupérer l'ID de la réservation
$id_reservation = $conn->lastInsertId();

// 3) INSERT dans menage
$stmt = $conn->prepare("
    INSERT INTO menage 
    (id_reservation, id_personnel, date_prevue, date_validation, statut)
    VALUES (?, ?, ?, ?, 'attente')
");

$stmt->execute([
    $id_reservation,
    $id_menage,
    $date_,      // ménage prévu le jour de la réservation
    null         // pas encore validé
]);


header("Location: ./index.php?page=responsable");
exit;
