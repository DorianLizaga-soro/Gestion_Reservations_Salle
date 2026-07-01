<?php
require_once __DIR__ . "/connexionBDD.php";

// 🔥 Nombre de nouvelles réservations
$stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE statut = 'en_attente'");
$stmt->execute();
$nbResa = $stmt->fetchColumn();

// 🔥 Nombre de nouveaux messages
$stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM messages 
    WHERE lu = 0 AND id_auteur != ?
");
$stmt->execute([$_SESSION["id"]]);
$nbMessages = $stmt->fetchColumn();

echo json_encode([
    "reservations" => $nbResa,
    "messages" => $nbMessages,
    "total" => $nbResa + $nbMessages
]);
