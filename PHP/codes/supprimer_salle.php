<?php
require_once(__DIR__ . '/connexionBDD.php');



$id_salle = $_POST["id_salle"];

$stmt = $conn->prepare("DELETE FROM salles WHERE id = ?");
$stmt->execute([$id_salle]);

header("Location: ./index.php?page=gestionnaire");
exit;

?>