<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: /ReservationSalle/index.php?page=login");
    exit;
}

$id_reservation = $_POST["id_reservation"];

if (isset($_FILES["fichier"]) && $_FILES["fichier"]["error"] === 0) {

    $nomOriginal   = basename($_FILES["fichier"]["name"]);
    $extension     = pathinfo($nomOriginal, PATHINFO_EXTENSION);
    $nomFichier    = uniqid() . '.' . $extension;
    
    $dossier       = __DIR__ . '/../../uploads/';
    $cheminServeur = $dossier . $nomFichier;
    $cheminBDD     = 'uploads/' . $nomFichier;

    if (!is_dir($dossier)) {
        mkdir($dossier, 0777, true);
    }

    if (move_uploaded_file($_FILES["fichier"]["tmp_name"], $cheminServeur)) {
        $stmt = $conn->prepare("INSERT INTO pdfs (id_reservation, nom_fichier, chemin) VALUES (?, ?, ?)");
        $stmt->execute([$id_reservation, $nomOriginal, $cheminBDD]);
    }
}

header("Location: /ReservationSalle/index.php?page=responsable");
exit;