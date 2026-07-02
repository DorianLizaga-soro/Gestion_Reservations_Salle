<?php
require_once(__DIR__ . '/connexionBDD.php');

// Ajouter commentaire
$stmt = $conn->prepare("INSERT INTO commentaires (id_reservation, id_auteur, contenu, date_creation)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['id_reservation'],
            $_SESSION['id'],
            $_POST['commentaire'],
            $_POST['date_creation']
        ]);

        header("Location: index.php?page=gestionnaire");
        exit;
    

?>