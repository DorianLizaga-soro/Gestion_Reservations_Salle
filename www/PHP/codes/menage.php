<?php

require_once(__DIR__ . '/connexionBDD.php');
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');


function dateFr($dateSQL) {
    $jours = [
        'Sunday' => 'Dimanche',
        'Monday' => 'Lundi',
        'Tuesday' => 'Mardi',
        'Wednesday' => 'Mercredi',
        'Thursday' => 'Jeudi',
        'Friday' => 'Vendredi',
        'Saturday' => 'Samedi'
    ];

    $mois = [
        'January' => 'janvier',
        'February' => 'février',
        'March' => 'mars',
        'April' => 'avril',
        'May' => 'mai',
        'June' => 'juin',
        'July' => 'juillet',
        'August' => 'août',
        'September' => 'septembre',
        'October' => 'octobre',
        'November' => 'novembre',
        'December' => 'décembre'
    ];

    $timestamp = strtotime($dateSQL);

    $jour = $jours[date('l', $timestamp)];
    $num = date('d', $timestamp);
    $moisTxt = $mois[date('F', $timestamp)];

    return "$jour $num $moisTxt";
}

if (!isset($_SESSION["id"])) {
    header("Location: /index.php?page=login");
    exit;
}
$sql = "
SELECT 
    M.*, 
    R.id_salle,
    R.id_association,
    S.nom AS nom_salle,
    A.nom AS nom_assos,
    A.id_responsable,
    U.nom AS nom_responsable
FROM Menage M
JOIN Reservations R ON M.id_reservation = R.id
JOIN Salles S ON R.id_salle = S.id
JOIN Associations A ON R.id_association = A.id
JOIN Utilisateurs U ON A.id_responsable = U.id
WHERE M.statut != 'fait'
ORDER BY M.date_prevue ASC
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$menages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "
SELECT 
    M.*, 
    R.id_salle,
    R.id_association,
    S.nom AS nom_salle,
    A.nom AS nom_assos,
    A.id_responsable,
    U.nom AS nom_responsable
FROM Menage M
JOIN Reservations R ON M.id_reservation = R.id
JOIN Salles S ON R.id_salle = S.id
JOIN Associations A ON R.id_association = A.id
JOIN Utilisateurs U ON A.id_responsable = U.id
WHERE M.statut = 'fait'
ORDER BY M.date_validation DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$menagesValides = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cartesHTML = "";
$cartesValidation ="";

foreach ($menages as $m) {

    $dateFormatee = dateFr($m["date_prevue"]);

    // Badge
    $badge = match($m["statut"]) {
    "a_faire" => "À faire",
    "attente" => "En attente",
    "fait" => "Effectué",
    default => "En attente"
};

$iconeBadge = "";
$classeBadge = "";

if ($m["statut"] === "fait") {
    $iconeBadge = "<i class='fa-regular fa-circle-check' style='color: rgb(41, 85, 0);'></i>";
    $classeBadge = "valide";
}
elseif ($m["statut"] === "attente") {
    $iconeBadge = "<i class='fa-solid fa-triangle-exclamation' style='color: rgb(148, 148, 148);'></i>";
    $classeBadge = "attente";
}
else { 
    $iconeBadge = "<i class='fa-regular fa-clock' style='color: rgb(146, 64, 14);'></i>";
}

    // Commentaires
    $stmt = $conn->prepare("SELECT contenu FROM Commentaires WHERE id_reservation = ?");
    $stmt->execute([$m["id_reservation"]]);
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $blocCommentaires = "";
    foreach ($commentaires as $c) {
        $blocCommentaires .= "<p class='commentaire'><i class='fa-solid fa-comment' style='color: rgb(148, 148, 148);'></i> {$c['contenu']}</p>";
    }
    if (!$blocCommentaires) $blocCommentaires = "<p class='commentaire'>Aucun commentaire</p>";

    // Carte
    $cartesHTML .= "
    <div class='card_menage' data-statut='{$m["statut"]}'>
        <div class='card_header'>
            <h3>{$m["nom_salle"]}</h3>
            <span class='badge {$classeBadge}'>
    {$iconeBadge} {$badge}
</span>

        </div>

        <p><i class='fa-regular fa-calendar'></i> {$dateFormatee}</p>
        <p class='nom_assos'>{$m["nom_assos"]}</p>

        <div class='div_resp'>
            <span class='initial_resp'>" . substr($m["nom_responsable"], 0, 2) . "</span>
            <p>{$m["nom_responsable"]}</p>
        </div>

        <div class='div_commentaire'>{$blocCommentaires}</div>

        <div class='div_btn_menage'>
            <form method='POST'>
                <input type='hidden' name='id_menage' value='{$m["id"]}'>
                " . ($m["statut"] === "a_faire" ? "
                    <button name='action' value='valider' class='btn_valider'>
                        <i class='fa-regular fa-circle-check'></i> Valider le passage
                    </button>
                " : "") . "

            </form>
            <button class='btn_commentaire'><i class='fa-regular fa-message'></i></button>
        </div>
        <div class='partie_commentaire'>
        <form method='POST'>
            <input type='hidden' name='id_reservation' value='{$m["id_reservation"]}'>
            <input type='hidden' name='date_creation' value='" . date("Y-m-d H:i:s") . "'>
            <input name='commentaire' id='input_commentaire' type='text' maxlength='50' placeholder='Commentaire...(50 car.)'>
            <button class='btn_ok'>OK</button>
            <div id='info' class='info'>0 / 50 caractères</div>
        </form>
    </div>
    </div>";
}


foreach ($menagesValides as $mv) {

    $stmt = $conn->prepare("SELECT contenu FROM Commentaires WHERE id_reservation = ?");
    $stmt->execute([$mv["id_reservation"]]);
    $commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $blocCommentaires = "";
    foreach ($commentaires as $c) {
        $blocCommentaires .= "<p class='commentaire'><i class='fa-solid fa-comment' style='color: rgb(148, 148, 148);'></i> {$c['contenu']}</p>";
    }
    if (!$blocCommentaires) $blocCommentaires = "<p class='commentaire'>Aucun commentaire</p>";

    $cartesValidation .= "
    <div class='div_passage'>
        <div class='div_historique'>
            <p><i class='fa-regular fa-circle-check' style='color: rgb(14,255,0);'></i></p>
            <p>{$mv["date_validation"]}</p>
            <p>{$mv["nom_salle"]}</p>
            <p>{$mv["nom_assos"]}</p>
        </div>
        <div class='div_comHistorique'>
            <p>{$mv["nom_responsable"]}</p>
            <p>{$blocCommentaires}</p>
        </div>
    </div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'valider') {

    $stmt = $conn->prepare("
        UPDATE Menage 
        SET statut = 'fait', date_validation = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$_POST['id_menage']]);

    header("Location: index.php?page=menage");
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmt = $conn->prepare("INSERT INTO Commentaires (id_reservation, id_auteur, contenu, date_creation) VALUES (:id_r, :id_a, :co, :d_c)
    ");

    $stmt->execute([
        'id_r' => $_POST['id_reservation'],
        'id_a' => $_SESSION["id"],
        'co'   => $_POST['commentaire'],
        'd_c'  => $_POST['date_creation']
    ]);

  
    header("Location: index.php?page=menage");
    exit;
}


$variables = [
    "{{nomPersonnelMenage}}" => $_SESSION["nom"],
    "{{cartesMenage}}" => $cartesHTML,
    "{{carteValidation}}"=> $cartesValidation
];


$template = file_get_contents(__DIR__ . '/../../html/menage.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);


echo $page;
