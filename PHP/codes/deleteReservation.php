<?php
require_once(__DIR__ . '/connexionBDD.php');

session_start();

if (!isset($_SESSION["id_createur"])) {
    header("Location: /Project_Gestion_Reservations_Salle139_VS/index.php?page=login");
    exit;
}

$id_reservation = $_GET["id"] ?? "";

if ($id_reservation === "") {
    header("Location: /Gestion_Reservations_Salle/index.php?page=responsable&erreur=id_manquant");
    exit;
}

$stmt = $conn->prepare("SELECT programme_pdf FROM reservations WHERE id = ?");
$stmt->execute([$id_reservation]);
$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    header("Location: /Gestion_Reservations_Salle/index.php?page=responsable&erreur=reservation_introuvable");
    exit;
}

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

header("Location: /Gestion_Reservations_Salle/index.php?page=reservation");
exit;
