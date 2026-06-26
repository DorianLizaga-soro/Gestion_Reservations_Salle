<?php


require_once(__DIR__ . '/connexionBDD.php');




if (!isset($_SESSION["id"])) {
    header("Location: /index.php?page=login");
    exit;
}



// nom du responsable
$stmt = $conn->prepare("SELECT * FROM utilisateurs");
$stmt->execute();
$nomResponsable = $stmt->fetch(PDO::FETCH_ASSOC);

$nom = $nomResponsable["nom"];
$role = $nomResponsable["role"];

//nombre tout reservation
$stmt = $conn->prepare("SELECT COUNT(*)  FROM reservations");
$stmt->execute();
$nbReservation = $stmt->fetchColumn();

//nb reservation recurrente

$stmt = $conn->prepare("SELECT COUNT(*)  FROM reservations WHERE type = 'recurrente'");
$stmt->execute();
$nbReservationRecurente = $stmt->fetchColumn();

//nb reservation ponctuelle

$stmt = $conn->prepare("SELECT COUNT(*)  FROM reservations WHERE type = 'recurrente'");
$stmt->execute();
$nbReservationPonctuelle = $stmt->fetchColumn();

//nb de passages de ménage à faire 

$stmt = $conn->prepare("SELECT COUNT(*)  FROM menage WHERE statut = 'a_faire'");
$stmt->execute();
$nbPassageDeMenage = $stmt->fetchColumn();

// carte reservations


$stmt = $conn->prepare("
    SELECT r.*, a.nom AS nom_association, s.nom AS nom_salle
    FROM reservations r
    LEFT JOIN associations a ON r.id_association = a.id
    LEFT JOIN salles s ON r.id_salle = s.id
    ORDER BY r.date_ ASC
");
$stmt->execute();
$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);





$cartesHTML = "";


foreach($reservation as $r) {

    $stmt = $conn->prepare("SELECT nom FROM salles WHERE id = ?");
    $stmt->execute([$r["id_salle"]]);
    $nomSalle = $stmt->fetchColumn();



    $badge = match($r["statut"]) {
        "en_attente" => "En attente",
        "validee" => "Valider",
        "annulee" => "Annuler",
        default => "Inconnu"
    };



$carte = "
    <div class='reservation-item reservation-blue'>
        <div class='reservation-date'>
            <strong>" . date("d M", strtotime($r["date_"])) . "</strong>
            <span>" . date("H:i", strtotime($r["heure_debut"])) . " - " . date("H:i", strtotime($r["heure_fin"])) . "</span>
        </div>

        <div class='reservation-info'>
            <strong>{$r["nom_association"]}</strong>
            <span>{$r["nom_salle"]}</span>
        </div>

        <div class='reservation-actions'>
            <span class='reservation-type type-recurrent'>
                <i class='bi bi-arrow-repeat'></i>
            </span>

            <button type='button' class='btn btn-success'
                style='--bs-btn-padding-y: .25rem; --bs-btn-padding-x: .5rem; --bs-btn-font-size: .75rem;'>
                {$badge}
            </button>
        </div>
    </div>
";

    $cartesHTML .= $carte;

}



$variables = [
    
    "{{carteReservation}}" => $cartesHTML,
    "{{nom}}" => $nom,
    "{{role}}" => $role,
    "{{initial}}" => substr($nom, 0, 2),
    "{{nbReservation}}"=>$nbReservation,
    "{{nbReservationRecurrente}}"=>$nbReservationRecurente,
    "{{nbPassageDeMenage}}"=>$nbPassageDeMenage
    

];


$template = file_get_contents(__DIR__ . '/../../HTML/dashboard.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);


echo $page;