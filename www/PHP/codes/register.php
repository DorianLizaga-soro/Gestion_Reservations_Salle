<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_once(__DIR__ . '/connexionBDD.php');  

    $action = $_POST["action"] ?? null;
    $identifiant = $_POST["identifiant"] ?? null;
    $email = $_POST["email"] ?? null;
    $mdp = $_POST["mdp"] ?? null;
    $role = $_POST["select_role"] ?? null;

    if ($action === "register") {

        if (empty($mdp)) {
            die("Mot de passe manquant");
        }

        $hash = password_hash($mdp, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO Utilisateurs(nom, email, password, role) VALUES (?,?,?,?)");
        $stmt->execute([$identifiant, $email, $hash, $role]);
    }

    $stmt = $conn->prepare("SELECT * FROM Utilisateurs WHERE nom = ?");
    $stmt->execute([$identifiant]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user["password"])) {
        $_SESSION["id"] = $user["id"];
        $_SESSION["nom"] = $user["nom"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["mdp"] = $user["password"];
        $_SESSION["select_role"] = $user["role"];
    }

    exit;
}

include __DIR__ . '/../../html/register.html';
