<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/connexionBDD.php');

header('Content-Type: application/json');

// GET uniquement : ce endpoint ne fait que lire une réservation,
// utilisé par les boutons "Voir" et "Modifier" pour pré-remplir la modale.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

if (empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Identifiant manquant']);
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $conn->prepare("
        SELECT
            reservations.id,
            reservations.id_association,
            reservations.id_salle,
            reservations.id_creneau_recurrent,
            reservations.type,
            reservations.date_,
            reservations.heure_debut,
            reservations.heure_fin,
            reservations.description,
            reservations.commentaire,
            reservations.programme_pdf,
            reservations.statut,
            reservations.id_createur,
            reservations.date_creation,
            associations.nom AS nomAssociation,
            salles.nom AS nomSalle,
            utilisateurs.nom AS nomResponsable
        FROM reservations
        INNER JOIN salles ON reservations.id_salle = salles.id
        INNER JOIN associations ON associations.id = reservations.id_association
        INNER JOIN utilisateurs ON utilisateurs.id = reservations.id_createur
        WHERE reservations.id = :id
    ");
    $stmt->execute(['id' => $id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Réservation introuvable']);
        exit;
    }

    echo json_encode(['success' => true, 'reservation' => $reservation]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
