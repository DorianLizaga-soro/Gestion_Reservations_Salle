<?php
require_once(__DIR__ . '/connexionBDD.php');

$type = $_GET['type'] ?? null;
$id   = $_GET['id'] ?? null;

if (!$type || !$id) {
    header("Location: index.php?page=exportations");
    exit;
}

if ($type === "excel") {

    // Récupérer le fichier
    $stmt = $conn->prepare("SELECT chemin FROM excel WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $cheminServeur ="./" . $file['chemin'];

        if (file_exists($cheminServeur)) {
            unlink($cheminServeur);
        }

        $stmt = $conn->prepare("DELETE FROM excel WHERE id = ?");
        $stmt->execute([$id]);
    }

} elseif ($type === "pdf") {

    $stmt = $conn->prepare("SELECT chemin FROM pdfs WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $cheminServeur ="./" . $file['chemin'];

        if (file_exists($cheminServeur)) {
            unlink($cheminServeur);
        }

        $stmt = $conn->prepare("DELETE FROM pdfs WHERE id = ?");
        $stmt->execute([$id]);
    }
} elseif ($type === "calendar") {

    $stmt = $conn->prepare("SELECT chemin FROM calendar WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $cheminServeur ="./" . $file['chemin'];

        if (file_exists($cheminServeur)) {
            unlink($cheminServeur);
        }

        $stmt = $conn->prepare("DELETE FROM calendar WHERE id = ?");
        $stmt->execute([$id]);
    }
}


header("Location: index.php?page=gestionnaire");
exit;
