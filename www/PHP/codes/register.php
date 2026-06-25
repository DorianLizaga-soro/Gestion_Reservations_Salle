<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_once(__DIR__ . '/connexionBDD.php');  

    $action = $_POST["action"] ?? null;
    $identifiant = $_POST["identifiant"] ?? null;
    $email = $_POST["email"] ?? null;
    $mdp = $_POST["mdp"] ?? null;
    $role = $_POST["select_role"] ?? null;
    $id_association = $_POST["id_association"] ?? null;


    if ($action === "register") {

        if (empty($identifiant) || empty($email) || empty($mdp) || empty($role)) {
            die("Champs manquants");
        }

        $hash = password_hash($mdp, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("
    INSERT INTO Utilisateurs(nom, email, password, role, id_association) 
    VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$identifiant, $email, $hash, $role, $id_association]);

    }

    // Redirection vers la page utilisateur
    header("Location: /index.php?page=utilisateur");
    exit;
}
