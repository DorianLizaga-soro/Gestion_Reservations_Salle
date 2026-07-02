<?php
require_once(__DIR__ . '/connexionBDD.php');

$id_reservation = $_POST["id_reservation"];
$contenu = trim($_POST["contenu"]);
$id_auteur = $_SESSION["id"];

if (!empty($contenu) && !empty($id_reservation)) {
    $stmt = $conn->prepare("INSERT INTO messages (id_reservation, id_auteur, contenu) VALUES (?, ?, ?)");
    $stmt->execute([$id_reservation, $id_auteur, $contenu]);
}

// Réponse AJAX
echo json_encode([
    "success" => true
]);
exit;
