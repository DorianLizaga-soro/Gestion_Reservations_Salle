<?php
require_once(__DIR__ . '/connexionBDD.php');

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: /ReservationSalle/index.php?page=exportations");
    exit;
}

$stmt = $conn->prepare("SELECT nom_fichier, chemin FROM pdfs WHERE id = ?");
$stmt->execute([$id]);
$pdf = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pdf) {
    header("Location: /ReservationSalle/index.php?page=exportations");
    exit;
}

$cheminFichier = __DIR__ . '/../../' . $pdf["chemin"];

if (!file_exists($cheminFichier)) {
    echo "Fichier introuvable.";
    exit;
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $pdf["nom_fichier"] . '.pdf"');
readfile($cheminFichier);
exit;