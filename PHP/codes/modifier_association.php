<?php
require_once(__DIR__ . '/connexionBDD.php');

$id_association = $_POST["id_association"];
$nom            = $_POST["nom"];
$couleur        = $_POST["couleur"];
$id_responsable = $_POST["id_responsable"];

// Récupérer l'ancien responsable
$stmt = $conn->prepare("SELECT id_responsable FROM associations WHERE id = ?");
$stmt->execute([$id_association]);
$ancienResponsable = $stmt->fetchColumn();

// Détacher l'ancien responsable de l'association
$stmt = $conn->prepare("UPDATE utilisateurs SET id_association = NULL WHERE id = ?");
$stmt->execute([$ancienResponsable]);

// Mettre à jour l'association
$stmt = $conn->prepare("UPDATE associations SET nom = ?, couleur = ?, id_responsable = ? WHERE id = ?");
$stmt->execute([$nom, $couleur, $id_responsable, $id_association]);

// Attacher le nouveau responsable à l'association
$stmt = $conn->prepare("UPDATE utilisateurs SET id_association = ? WHERE id = ?");
$stmt->execute([$id_association, $id_responsable]);

header("Location: ./index.php?page=gestionnaire");
exit;