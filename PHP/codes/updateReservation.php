<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/connexionBDD.php');

function json_input(): array {
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

$data = json_input();

try {
    $stmt = $conn->prepare(
        'UPDATE reservations
         SET association=:association, salle=:salle, date=:date, startTime=:startTime,
             endTime=:endTime, type=:type, description=:description, comment=:comment
         WHERE id=:id'
    );

    $ok = $stmt->execute([
        ':association' => $data['association'] ?? null,
        ':salle' => $data['salle'] ?? null,
        ':date' => $data['date'] ?? null,
        ':startTime' => $data['startTime'] ?? null,
        ':endTime' => $data['endTime'] ?? null,
        ':type' => $data['type'] ?? null,
        ':description' => $data['description'] ?? null,
        ':comment' => $data['comment'] ?? null,
        ':id' => $data['id'] ?? null,
    ]);

    echo json_encode(['success' => (bool)$ok]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

