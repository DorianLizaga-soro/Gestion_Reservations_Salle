<?php

header("Content-Type: application/json");
require "connexionBDD.php";

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("DELETE FROM reservations WHERE id=?");
$stmt->bind_param("i", $data["id"]);
$stmt->execute();

echo json_encode(["success" => true]);

?>