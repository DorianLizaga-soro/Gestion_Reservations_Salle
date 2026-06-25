<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_once(__DIR__ . '/connexionBDD.php');

    $id = $_POST["id_user"] ?? null;

    if (!$id) {
        header("Location: /index.php?page=utilisateur&error=missing_id");
        exit;
    }

    // Vérifier si l'utilisateur est responsable d'une association
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Associations WHERE id_responsable = ?");
    $stmt->execute([$id]);
    $isResponsable = $stmt->fetchColumn();

    if ($isResponsable > 0) {
        header("Location: /index.php?page=utilisateur&error=responsable");
        exit;
    }

    // Suppression
    $stmt = $conn->prepare("DELETE FROM Utilisateurs WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: /index.php?page=utilisateur&success=deleted");
    exit;
}
