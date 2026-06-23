<?php

require_once(__DIR__ . '/connexionBDD.php');


if (!isset($_SESSION["id"])) {
    header("Location: /index.php?page=login");
    exit;
}

//salle
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

// carte menage

$stmt = $conn->prepare("SELECT * FROM Menage");
$stmt->execute();
$menages = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$menages) {
    $menages = [];
}

$cartesHTML = "";

foreach($menages as $m) {
    $badge = match($m["statut"]) {
        "a_faire" => "À faire",
        "effectue" => "Effectué",
        "attente" => "En attente",
        default => "Inconnu"
    };

    $carte = "
    <div class='card_menage'>
        <div class='card_header'>
            <h3>{$nomSalle}</h3>
            <span class='badge'>{$badge}</span>
        </div>

        <p><i class='fa-regular fa-calendar'></i> {$m["date_prevue"]}</p>
        <p class='nom_assos'>{$nomAssos}</p>

        <div class='div_resp'>
            <span class='initial_resp'>" . substr($nomResponsable, 0, 2) . "</span>
            <p>{$nomResponsable}</p>
        </div>

        <p class='commentaire'>commentaire</p>

        <div>
            <button class='btn_valider'>Valider le passage</button>
            <button class='btn_commentaire'><i class='fa-regular fa-message'></i></button>
        </div>

        <div class='partie_commentaire'>
            <input id='input_commentaire' type='text' placeholder='Commentaire...(50 car.)'>
            <button class='btn_ok'>OK</button>
        </div>
    </div>
    ";

    $cartesHTML .= $carte;
}

$variables = [
    "{{nomPersonnelMenage}}" => $_SESSION["nom"],
    "{{cartesMenage}}" => $cartesHTML
];


$template = file_get_contents(__DIR__ . '/../../html/menage.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);


echo $page;
