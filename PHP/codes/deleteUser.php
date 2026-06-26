<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_once(__DIR__ . '/connexionBDD.php');

    $id = $_POST["id_user"] ?? null;

    if (!$id) {
        header("Location: ./index.php?page=gestionnaire");
        exit;
    }

    // Vérifier si l'utilisateur est responsable d'une association
    $stmt = $conn->prepare("SELECT COUNT(*) FROM associations WHERE id_responsable = ?");
    $stmt->execute([$id]);
    $isResponsable = $stmt->fetchColumn();

    if ($isResponsable > 0) {
        header("Location: ./index.php?page=gestionnaire");
        exit;
    }

    // Suppression
    $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: ./index.php?page=gestionnaire");
    exit;
}
