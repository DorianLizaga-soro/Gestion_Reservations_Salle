<?php
require_once(__DIR__ . '/connexionBDD.php');


$nom            = $_POST["identifiant"];
$capacite          = $_POST["capacite"];
$description       = $_POST["description"];


$stmt = $conn->prepare("INSERT INTO salles (nom, capacite, description) 
    VALUES (?, ?, ?)");

$stmt->execute([$nom, $capacite, $description]);

header("Location: ./index.php?page=gestionnaire");
exit;

?>