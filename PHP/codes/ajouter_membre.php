<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: ./index.php?page=login");
    exit;
}

$nom            = $_POST["identifiant"];
$email          = $_POST["mail"];
$password       = password_hash($_POST["mot_de_passe"], PASSWORD_DEFAULT);
$id_association = $_SESSION["id_association"];

$stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, password, role, id_association) 
    VALUES (?, ?, ?, 'membre_association', ?)");

$stmt->execute([$nom, $email, $password, $id_association]);

header("Location: ./index.php?page=responsable");
exit;

?>


