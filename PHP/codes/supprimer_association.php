<?php
require_once(__DIR__ . '/connexionBDD.php');


$id_association = $_POST["id_association"];

$stmt = $conn->prepare("DELETE FROM associations WHERE id = ?");
$stmt->execute([$id_association]);

header("Location: ./index.php?page=associationAdmin");
exit;

?>