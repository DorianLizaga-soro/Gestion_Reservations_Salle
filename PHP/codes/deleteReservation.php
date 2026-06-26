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
    $stmt = $conn->prepare('DELETE FROM reservations WHERE id=:id');
    $ok = $stmt->execute([
        ':id' => $data['id'] ?? null,
    ]);

    echo json_encode(['success' => (bool)$ok]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

