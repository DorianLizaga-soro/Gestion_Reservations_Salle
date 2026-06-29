<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservation_salle";

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed"
    ]);
    exit;
}

?>