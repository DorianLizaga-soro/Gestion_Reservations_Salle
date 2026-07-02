<?php
require_once(__DIR__ . '/connexionBDD.php');

$id = $_POST["id"] ?? null;

if (!$id) {
    header("Location: index.php?page=panneaudaffichage");
    exit;
}

// Récupérer le chemin du fichier
$stmt = $conn->prepare("SELECT chemin FROM pdfs WHERE id = ?");
$stmt->execute([$id]);
$pdf = $stmt->fetch(PDO::FETCH_ASSOC);

if ($pdf) {
    $chemin = $pdf["chemin"];

    // Supprimer le fichier du serveur
    if (file_exists($chemin)) {
        unlink($chemin);
    }

    // Supprimer la ligne dans la BDD
    $stmt = $conn->prepare("DELETE FROM pdfs WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php?page=gestionnaire");
exit;
