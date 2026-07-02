<?php
require_once(__DIR__ . '/connexionBDD.php');

$id = $_POST["id_salle"];
$nom            = $_POST["identifiant"];
$capacite          = $_POST["capacite"];
$description       = $_POST["description"];

$stmt = $conn->prepare("UPDATE salles 
    SET nom = ?, capacite= ?, description = ?
    WHERE id = ?");

$stmt->execute([$nom, $capacite, $description,$id]);

header("Location: ./index.php?page=gestionnaire");
exit;