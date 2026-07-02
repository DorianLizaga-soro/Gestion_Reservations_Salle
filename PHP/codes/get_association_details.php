<?php
require_once(__DIR__ . '/connexionBDD.php');

header('Content-Type: application/json');

$id = $_GET["id"] ?? null;

if (!$id) {
    echo json_encode(["error" => "id manquant"]);
    exit;
}

// Infos de base
$stmt = $conn->prepare("SELECT * FROM associations WHERE id = ?");
$stmt->execute([$id]);
$asso = $stmt->fetch(PDO::FETCH_ASSOC);

// Membres
$stmt = $conn->prepare("SELECT nom, role FROM utilisateurs WHERE id_association = ? LIMIT 3");
$stmt->execute([$id]);
$membres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Réservations du mois (calendrier)
$stmt = $conn->prepare("SELECT r.*, s.nom as nom_salle FROM reservations r 
    JOIN salles s ON r.id_salle = s.id
    WHERE r.id_association = ? AND DATE_FORMAT(r.date_, '%Y-%m') = ?
    ORDER BY r.date_");
$stmt->execute([$id, date("Y-m")]);
$reservationsMois = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Historique (toutes les réservations)
$stmt = $conn->prepare("SELECT r.*, s.nom as nom_salle FROM reservations r 
    JOIN salles s ON r.id_salle = s.id
    WHERE r.id_association = ?
    ORDER BY r.date_ DESC");
$stmt->execute([$id]);
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Documents
$stmt = $conn->prepare("SELECT p.* FROM pdfs p 
    JOIN reservations r ON p.id_reservation = r.id
    WHERE r.id_association = ?");
$stmt->execute([$id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "asso"            => $asso,
    "membres"         => $membres,
    "reservationsMois" => $reservationsMois,
    "historique"      => $historique,
    "documents"       => $documents
]);