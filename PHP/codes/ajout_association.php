<?php
require_once(__DIR__ . '/connexionBDD.php');

$nom        = $_POST["nom_assos"];
$couleur    = $_POST["couleur"];
$responsable = $_POST["responsable_assos"];

// Ajouter l'association
$stmt = $conn->prepare("INSERT INTO associations (nom, couleur, id_responsable) VALUES (?, ?, ?)");
$stmt->execute([$nom, $couleur, $responsable]);

// Récupérer l'id de la nouvelle association
$idNouvelleAsso = $conn->lastInsertId();

// Mettre à jour l'utilisateur responsable avec l'id de la nouvelle association
$stmt = $conn->prepare("UPDATE utilisateurs SET id_association = ? WHERE id = ?");
$stmt->execute([$idNouvelleAsso, $responsable]);

header("Location: /ReservationSalle/index.php?page=associationAdmin");
exit;

?>