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

$stmt = $conn->prepare("INSERT INTO reservations 
    (id_association, id_salle, type, date_, heure_debut, heure_fin, statut, id_createur, Motif) 
    VALUES (?, ?, 'ponctuelle', ?, ?, ?, 'en_attente', ?, ?)");

$stmt->execute([$id_association, $id_salle, $date_, $heure_debut, $heure_fin, $id_createur, $motif]);

header("Location: ./index.php?page=responsable");
exit;