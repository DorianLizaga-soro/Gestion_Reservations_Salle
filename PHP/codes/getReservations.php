<?php
header('Content-Type: application/json');
require_once(__DIR__ . '/connexionBDD.php');

try {
    $stmt = $conn->query('SELECT * FROM reservations ORDER BY date ASC');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

