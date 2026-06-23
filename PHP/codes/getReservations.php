<?php

header("Content-Type: application/json");
require "connexionBDD.php";

$sql = "SELECT * FROM reservations ORDER BY date ASC";
$result = $conn->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

?>