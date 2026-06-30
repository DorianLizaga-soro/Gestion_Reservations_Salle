<?php
require_once(__DIR__ . '/connexionBDD.php');


$id_reservation = $_POST["id"] ?? "";



$stmt = $conn->prepare("SELECT programme_pdf FROM reservations WHERE id = ?");
$stmt->execute([$id_reservation]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);



$stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
$stmt->execute([$id_reservation]);

// Le fichier joint n'est supprimé qu'une fois la ligne effectivement
// supprimée en base, pour ne jamais perdre un fichier référencé.
if (!empty($reservation["programme_pdf"])) {
    $chemin = __DIR__ . "/../../" . $reservation["programme_pdf"];
    if (is_file($chemin)) {
        unlink($chemin);
    }
}

unset( $_SESSION["error_message"]);
$_SESSION["error_message"][]='<div class="alert alert-success">La réservation a été supprimée</div>';

header("Location: ./index.php?page=gestionnaire");
exit;