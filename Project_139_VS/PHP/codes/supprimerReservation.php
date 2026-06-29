<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/connexionBDD.php');

header('Content-Type: application/json');

// Accepté en POST plutôt qu'en DELETE : déclenchable depuis un simple
// fetch() sans configuration CORS supplémentaire, cohérent avec le reste
// de l'API qui ne fait déjà que du GET/POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

if (empty($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Identifiant manquant']);
    exit;
}

$id = $_POST['id'];

try {
    $stmt = $conn->prepare("SELECT programme_pdf FROM reservations WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Réservation introuvable']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = :id");
    $ok = $stmt->execute(['id' => $id]);

    // Le fichier joint n'est supprimé qu'une fois la ligne effectivement
    // supprimée en base, pour ne jamais perdre un fichier référencé.
    if ($ok && !empty($reservation['programme_pdf'])) {
        $chemin = __DIR__ . '/../../' . $reservation['programme_pdf'];
        if (is_file($chemin)) {
            unlink($chemin);
        }
    }

    echo json_encode(['success' => (bool) $ok]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
