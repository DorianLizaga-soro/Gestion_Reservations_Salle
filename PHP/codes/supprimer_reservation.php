<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: ./index.php?page=login");
    exit;
}

$id_reservation = $_POST["id_reservation"];

$stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
$stmt->execute([$id_reservation]);

header("Location: ./index.php?page=responsable");
exit;

?>

