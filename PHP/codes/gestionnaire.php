<?php


require_once(__DIR__ . '/connexionBDD.php');




if (!isset($_SESSION["id"])) {
    header("Location: /index.php?page=login");
    exit;
}



// nom du responsable
$stmt = $conn->prepare("SELECT * FROM utilisateurs");
$stmt->execute();
$nomResponsable = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE role ='gestionnaire'");
$stmt->execute();
$gestionnaire = $stmt->fetchAll(PDO::FETCH_ASSOC);

//nombre tout reservation
$stmt = $conn->prepare("SELECT COUNT(*)  FROM reservations");
$stmt->execute();
$nbReservation = $stmt->fetchColumn();

//Nombre de salle

$stmt = $conn->prepare("SELECT * FROM salles ");
$stmt->execute();
$salle = $stmt->fetchAll(PDO::FETCH_ASSOC);

//nb reservation recurrente

$stmt = $conn->prepare("SELECT COUNT(*)  FROM reservations WHERE type = 'recurrente'");
$stmt->execute();
$nbReservationRecurente = $stmt->fetchColumn();

//nb reservation ponctuelle

$stmt = $conn->prepare("SELECT COUNT(*)  FROM reservations WHERE type = 'ponctuelle'");
$stmt->execute();
$nbReservationPonctuelle = $stmt->fetchColumn();

//nb de passages de ménage à faire 

$stmt = $conn->prepare("SELECT COUNT(*)  FROM menage WHERE statut = 'a_faire'");
$stmt->execute();
$nbPassageDeMenage = $stmt->fetchColumn();


//carte commentaires

$stmt = $conn->prepare("SELECT *, U.nom AS nom_auteur FROM commentaires JOIN utilisateurs U ON commentaires.id_auteur = U.id ");
$stmt->execute();
$commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $conn->prepare("SELECT COUNT(*)  FROM commentaires");
$stmt->execute();
$nbCommentaire = $stmt->fetchColumn();


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

$stmt = $conn->prepare("
    SELECT a.nom AS nom_assos, COUNT(r.id) AS total, couleur
    FROM associations a
    LEFT JOIN reservations r ON r.id_association = a.id
    GROUP BY a.id
    ORDER BY total DESC
");
$stmt->execute();
$statsAssos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalAssos = count($statsAssos);





$cartesHTML = "";

$stmt = $conn->prepare("
    SELECT r.*, a.nom AS nom_association, a.couleur AS couleur_association, s.nom AS nom_salle
    FROM reservations r
    LEFT JOIN associations a ON r.id_association = a.id
    LEFT JOIN salles s ON r.id_salle = s.id
    ORDER BY r.date_ ASC
");
$stmt->execute();
$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events = [];



foreach ($reservation as $r) {


   // Vérifier que la date est au bon format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $r["date_"])) {
        continue;
    }

    // Vérifier que la date est réelle
    $dateObj = DateTime::createFromFormat('Y-m-d', $r["date_"]);
    if (!$dateObj) {
        continue;
    }

    // Vérifier les heures
    if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $r["heure_debut"])) continue;
    if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $r["heure_fin"])) continue;

    $events[] = [
        "title" => $r["nom_association"],
        "start" => $r["date_"] . "T" . $r["heure_debut"],
        "end"   => $r["date_"] . "T" . $r["heure_fin"],
        "color" => $r["couleur_association"], // couleur dynamique
        "type"  => $r["type"],
    ];




$couleur = $r["couleur_association"] ?? "#35CA6C";
    $badge = match($r["statut"]) {
        "en_attente" => "En attente",
        "validee" => "Validée",
        "annulee" => "Annulée",
        default => "Inconnu"
    };

    $carte = "
        <div class='reservation-item reservation-color' style='--color: {$couleur};' >
       
            <div class='reservation-date'>
                <strong>" . date("d M", strtotime($r["date_"])) . "</strong>
                <span>" . date("H:i", strtotime($r["heure_debut"])) . " - " . date("H:i", strtotime($r["heure_fin"])) . "</span>
            </div>

            <div class='reservation-info'>
                <strong>{$r["nom_association"]}</strong>
                <span>{$r["nom_salle"]}</span>
            </div>

            <div class='reservation-actions'>
                
                <span class='badge rounded-pill text-bg-success'>
                    {$badge}
                </span>
            </div>
        </div>
    ";

    $cartesHTML .= $carte;
}



$carteSalle= "";

foreach ($salle as $s) {

if($s["capacite"]!=""){
$capacite="{$s["capacite"]} personnes";
}
else{
$capacite="neant";
}
if($s["description"]!=""){
$description="{$s["description"]}";
}
else{
$description="neant";
}
    $carte2="
    <div class='reservation-item'>
        <div class='reservation-date'>
            <strong>{$s["nom"]}</strong>
        </div>

        <div class='reservation-info'>
            <strong>Capacité : $capacite</strong>
            <span>Description : $description</span>
        </div>

        <div class='reservation-actions'>
            <div class='btn_modifDelete'>
                <button class='btn_modif'
                    data-id='{$s["id"]}'
                    data-nom='{$s["nom"]}'
                    data-capacite='{$s["capacite"]}'
                    data-description='{$s["description"]}'>
                    <i class='fa-regular fa-pen-to-square' style='color: rgb(148, 148, 148);'></i>
                </button>
            </div> 
            <form action='index.php?page=delete_salle' method='POST' class='form_delete_user'>
                <input type='hidden' name='id_salle' value='{$s["id"]}'>
                <button class='btn_delete' type='submit'>
                <i class='fa-regular fa-trash-can' style='color: rgb(255, 0, 0);'></i>
                </button>
            </form>
        </div>
    </div>
        
    

    ";
    $carteSalle .= $carte2;
}

$carteCommentaires="";
foreach ($commentaires as $c) {

    $carte3="
        <div class='comment-item'>

            <div class='avatar'>" . substr($c["nom_auteur"], 0, 2) . "</div>

            <div>
                <strong>{$c["nom_auteur"]}</strong>
                <p>{$c["contenu"]}</p>
                <p>{$c["date_creation"]}</p>
            </div>

        </div>
    
    ";
    $carteCommentaires .= $carte3;
}

$carte4 = "";
$max = 15;
$carte5 = "";
$maxAffichage = 4;
$affichees = 0;
foreach ($statsAssos as $s) {

$couleur = $s["couleur"] ?? "#35CA6C";
    // Calcul du pourcentage
    $pourcentage = ($max > 0) ? ($s["total"] / $max) * 100 : 0;

    $carte4 .= "
        <div class='activity-item'>
            <span>{$s["nom_assos"]}</span>
            <strong>{$s["total"]}</strong>
        </div>

        <div class='progress'>
            <div class='progress-fill ' style='width: {$pourcentage}%;background-color:{$couleur};'></div>
        </div>

    ";
if ($affichees >= $maxAffichage) break; // 🔥 limite à 4
    $carte5 .= "
        <div class='association-legend'>
            <span><i class='legend-dot' style='background-color:{$couleur};'></i>{$s["nom_assos"]}</span>
        </div>
    ";

    $affichees++;
}

$carte6="";

foreach ($gestionnaire as $n){
   
$carte6 .="

<div class='sidebar-user'>
                    
    <div class='user-avatar'>" . substr($n["nom"], 0, 2) . "
    </div>

        <div>
            <strong>{$n["nom"]}</strong>
            <p>{$n["role"]}</p>
        </div>

        <form action='./index.php?page=deconnexion'>
            <button class='btn_sideBare' type='submit'><i class='bi bi-box-arrow-right ms-auto'></i></button>
        </form>


</div>";

}

$reservedDays = array_values(array_unique(array_column($reservation, "date_")));
$reste = $totalAssos - $affichees;

$variables = [
    
    "{{carteReservation}}" => $cartesHTML,
    "{{nbReservation}}"=>$nbReservation,
    "{{nbReservationRecurrente}}"=>$nbReservationRecurente,
    "{{nbPassageDeMenage}}"=>$nbPassageDeMenage,
    "{{nbReservationPonctuelles}}"=>$nbReservationPonctuelle,
    "{{carteSalle}}"=>$carteSalle,
    "{{carteCommentaires}}"=>$carteCommentaires,
    "{{nbCommentaire}}"=>$nbCommentaire,
    "{{activiteAssos}}"=>$carte4,
    "{{nomAssosCalendar}}"=>$carte5,
    "{{sidebarUser}}"=>$carte6,
    "{{calendarEvents}}" => "var calendarEvents = " . json_encode($events) . ";",
    "{{reservedDays}}" => "var reservedDays = " . json_encode($reservedDays) . ";",
    "{{resteAssos}}" => ($reste > 0 ? "+$reste" : ""),



    

];


$template = file_get_contents(__DIR__ . '/../../HTML/dashboard.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);


echo $page;