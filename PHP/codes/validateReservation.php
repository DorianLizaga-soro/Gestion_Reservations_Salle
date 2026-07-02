<?php
require_once(__DIR__ . '/connexionBDD.php');

$id = $_POST["id"] ?? "";

if (!$id) {
    $_SESSION["error_message"][] = '<div class="alert alert-danger">ID manquant</div>';
    header("Location: ./index.php?page=gestionnaire");
    exit;
}

$stmt = $conn->prepare("UPDATE reservations SET statut = 'validee' WHERE id = ?");
$stmt->execute([$id]);

$_SESSION["error_message"][] = '<div class="alert alert-success">Réservation validée</div>';

$stmt = $conn->prepare("
    UPDATE menage 
    SET statut = 'a_faire'
    WHERE id_reservation = ?
");
$stmt->execute([$id]);



header("Location: ./index.php?page=gestionnaire");
exit;
