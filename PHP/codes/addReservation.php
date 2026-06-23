<?php

header("Content-Type: application/json");
require "connexionBDD.php";

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("
    INSERT INTO reservations 
    (association, salle, date, startTime, endTime, type, description, comment)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssssss",
    $data["association"],
    $data["salle"],
    $data["date"],
    $data["startTime"],
    $data["endTime"],
    $data["type"],
    $data["description"],
    $data["comment"]
);

$stmt->execute();

echo json_encode(["success" => true]);

?>