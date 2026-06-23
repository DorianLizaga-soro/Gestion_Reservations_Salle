<?php

include __DIR__ . '/../../html/connexion.html';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_once(__DIR__ . '/connexionBDD.php');

    $action = $_POST["action"] ?? null;
    $identifiant = $_POST["identifiant"] ?? null;
    $mdp = $_POST["mdp"] ?? null;

    if ($action === "login") {

        $stmt = $conn->prepare("SELECT * FROM Utilisateurs WHERE nom = ?");
        $stmt->execute([$identifiant]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user["password"])) {

            $_SESSION["id"] = $user["id"];
            $_SESSION["nom"] = $user["nom"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["mdp"] = $user["password"];
            $_SESSION["select_role"] = $user["role"]; 

            $_SESSION["total"] = 0;

            // Redirection selon le rôle
            if ($user["role"] === "personnel_menage") {
               header("Location: /index.php?page=menage");
                exit;
            }

             if ($user["role"] === "gestionnaire") {
               header("Location: /index.php?page=gestionnaire");
                exit;
            }

            if ($user["role"] === "responsable_association") {
               header("Location: /index.php?page=responsable");
                exit;
            }

            if ($user["role"] === "membre_association") {
               header("Location: /index.php?page=membre");
                exit;
            }


        } else {
            echo "Identifiants incorrects";
        }
    }

    exit;
}
