<?php

header("Content-Type: application/json");
require "connexionBDD.php";

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("
    UPDATE reservations
    SET association=?, salle=?, date=?, startTime=?, endTime=?, type=?, description=?, comment=?
    WHERE id=?
");

$stmt->bind_param(
    "ssssssssi",
    $data["association"],
    $data["salle"],
    $data["date"],
    $data["startTime"],
    $data["endTime"],
    $data["type"],
    $data["description"],
    $data["comment"],
    $data["id"]
);

$stmt->execute();

echo json_encode(["success" => true]);

?>