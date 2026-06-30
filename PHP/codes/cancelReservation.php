<?php
require_once(__DIR__ . '/connexionBDD.php');

$id = $_POST["id"] ?? "";

if (!$id) {
    $_SESSION["error_message"][] = '<div class="alert alert-danger">ID manquant</div>';
    header("Location: ./index.php?page=gestionnaire");
    exit;
}

$stmt = $conn->prepare("UPDATE reservations SET statut = 'annulee' WHERE id = ?");
$stmt->execute([$id]);

$_SESSION["error_message"][] = '<div class="alert alert-warning">Réservation annulée</div>';

header("Location: ./index.php?page=gestionnaire");
exit;
