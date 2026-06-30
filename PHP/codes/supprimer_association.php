<?php
require_once(__DIR__ . '/connexionBDD.php');

$id_association = $_POST["id_association"];

// Détacher tous les utilisateurs de cette association
$stmt = $conn->prepare("UPDATE utilisateurs SET id_association = NULL WHERE id_association = ?");
$stmt->execute([$id_association]);

// Supprimer les réservations liées
$stmt = $conn->prepare("DELETE FROM reservations WHERE id_association = ?");
$stmt->execute([$id_association]);

// Supprimer l'association
$stmt = $conn->prepare("DELETE FROM associations WHERE id = ?");
$stmt->execute([$id_association]);

header("Location: ./index.php?page=gestionnaire");
exit;