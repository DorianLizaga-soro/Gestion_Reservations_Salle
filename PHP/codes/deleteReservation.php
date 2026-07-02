<?php
require_once(__DIR__ . '/connexionBDD.php');

$id_reservation = $_POST["id"] ?? "";

if (!$id_reservation) {
    header("Location: ./index.php?page=gestionnaire");
    exit;
}

// 1. Récupérer tous les PDF liés à la réservation
$stmt = $conn->prepare("SELECT chemin FROM pdfs WHERE id_reservation = ?");
$stmt->execute([$id_reservation]);
$pdfs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Supprimer les fichiers du serveur
foreach ($pdfs as $pdf) {
    $chemin = $pdf["chemin"];

    // Corriger le chemin si nécessaire
    if (file_exists($chemin)) {
        unlink($chemin);
    }
}

// 3. Supprimer les PDF dans la BDD
$stmt = $conn->prepare("DELETE FROM pdfs WHERE id_reservation = ?");
$stmt->execute([$id_reservation]);

// 4. Supprimer la réservation
$stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
$stmt->execute([$id_reservation]);

// 5. Message de confirmation
unset($_SESSION["error_message"]);
$_SESSION["error_message"][] = '<div class="alert alert-success">La réservation et ses PDF ont été supprimés</div>';

header("Location: ./index.php?page=gestionnaire");
exit;
