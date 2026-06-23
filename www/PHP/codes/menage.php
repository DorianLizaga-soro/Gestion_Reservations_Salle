<?php

require_once(__DIR__ . '/connexionBDD.php');


if (!isset($_SESSION["id"])) {
    header("Location: /index.php?page=login");
    exit;
}

$stmt = $conn->prepare("SELECT nom FROM Salles WHERE id = ?");
$stmt->execute([1]); 
$nomSalle = $stmt->fetchColumn();

// association
$stmt = $conn->prepare("SELECT nom, id_responsable FROM Associations WHERE id = ?");
$stmt->execute([1]);
$association = $stmt->fetch(PDO::FETCH_ASSOC);

$nomAssos = $association["nom"];
$idResponsable = $association["id_responsable"];

// nom du responsable
$stmt = $conn->prepare("SELECT nom FROM Utilisateurs WHERE id = ?");
$stmt->execute([$idResponsable]);
$nomResponsable = $stmt->fetchColumn();


$variables = [
    "{{nomPersonnelMenage}}" => $_SESSION["nom"],
    "{{nomDeLaSalle}}" => $nomSalle,
    "{{nomAssos}}" => $nomAssos,
    "{{Initial}}" => substr($nomResponsable, 0, 2),
    "{{nomResponsable}}" => $nomResponsable
];



$template = file_get_contents(__DIR__ . '/../../html/menage.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);


echo $page;
