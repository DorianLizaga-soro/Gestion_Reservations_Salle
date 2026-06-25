<?php
session_start();
require_once(__DIR__ . '/connexionBDD.php');

$id = $_POST["id_user"];
$nom = $_POST["identifiant"];
$email = $_POST["email"];
$role = $_POST["select_role"];
$association = $_POST["id_association"];
$mdp = $_POST["mdp"];

$hash = password_hash($mdp, PASSWORD_BCRYPT);

$stmt = $conn->prepare("
    UPDATE Utilisateurs 
    SET nom = ?, email = ?, role = ?, id_association = ?, password = ?
    WHERE id = ?
");

$stmt->execute([$nom, $email, $role, $association, $hash,$id]);

header("Location: /index.php?page=utilisateur");
exit;
