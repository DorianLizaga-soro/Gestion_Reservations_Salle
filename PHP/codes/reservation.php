<?php

require_once(__DIR__ . '/connexionBDD.php');

// <-- adapte cet ID à un utilisateur réel de ta table `utilisateurs`


/**************************************************************
 * FILTERS (GET)
 **************************************************************/
$where = [];
$params = [];

if (!empty($_GET['association'])) {
    $where[] = "reservations.id_association = ?";
    $params[] = $_GET['association'];
}

if (!empty($_GET['type'])) {
    $where[] = "reservations.type = ?";
    $params[] = $_GET['type'];
}

if (!empty($_GET['statut'])) {
    $where[] = "reservations.statut = ?";
    $params[] = $_GET['statut'];
}

/**************************************************************
 * MAIN QUERY (RESERVATIONS)
 **************************************************************/
$sql = "
    SELECT
        reservations.id,
        date_,
        heure_debut,
        heure_fin,
        associations.id AS id_association,
        associations.nom AS nomAssos,
        type,
        salles.nom AS Salle,
        statut,
        utilisateurs.nom AS nomResponsable,
        salles.id AS id_salle,
        reservations.Motif AS description,
        reservations.commentaire AS commentaire

    FROM reservations
    INNER JOIN salles ON reservations.id_salle = salles.id
    INNER JOIN associations ON associations.id = reservations.id_association
    INNER JOIN utilisateurs ON utilisateurs.id = reservations.id_createur
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY date_ DESC, heure_debut DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("SELECT id, nom FROM utilisateurs WHERE role = 'personnel_menage'");
$personnels = $stmt->fetchAll(PDO::FETCH_ASSOC);

$optionsMenage = "";
foreach ($personnels as $p) {
    $optionsMenage .= "<option value='{$p['id']}'>{$p['nom']}</option>";
}
/**************************************************************
 * TABLE ROWS HTML
 **************************************************************/
$cartesHTML = "";

foreach ($reservations as $r) {
    
    $badge = match ($r["statut"]) {
        "en_attente" => "En attente",
        "validee"    => "Validée",
        "annulee"    => "Annulée",
        default      => "Conflit"
    };
$btnAction = "";

if ($r["statut"] === "en_attente") {
    // Bouton VALIDER
    $btnAction = "
        <form action='index.php?page=validateReservation' method='POST'>
            <input type='hidden' name='id' value='{$r["id"]}'>
            <button class='btn btn-success btn-sm' type='submit'>Valider</button>
        </form>
    ";
}

if ($r["statut"] === "validee") {
    // Bouton ANNULER
    $btnAction = "
        <form action='index.php?page=cancelReservation' method='POST'>
            <input type='hidden' name='id' value='{$r["id"]}'>
            <button class='btn btn-warning btn-sm' type='submit'>Annuler</button>
        </form>
    ";
}

    $cartesHTML .= "
        <tr>
            <td>{$r["date_"]}</td>
            <td>{$r["heure_debut"]} - {$r["heure_fin"]}</td>
            <td>{$r["nomAssos"]}</td>
            <td>{$r["type"]}</td>
            <td>{$r["Salle"]}</td>
            <td>{$r["nomResponsable"]}</td>
            <td>{$badge}</td>
            <td class='td_btn'>
            <button class='btn btn-secondary btn-sm'
    data-id='{$r['id']}'
    data-association='{$r["id_association"]}'
    data-salle='{$r["id_salle"]}'
    data-type='{$r["type"]}'
    data-date='{$r["date_"]}'
    data-debut='{$r["heure_debut"]}'
    data-fin='{$r["heure_fin"]}'
    data-description='{$r["description"]}'
    data-commentaire='{$r["commentaire"]}'
>Voir</button>
<button class='btn btn-secondary btn-sm'
    data-id='{$r['id']}'
    data-association='{$r["id_association"]}'
    data-salle='{$r["id_salle"]}'
    data-type='{$r["type"]}'
    data-date='{$r["date_"]}'
    data-debut='{$r["heure_debut"]}'
    data-fin='{$r["heure_fin"]}'
    data-description='{$r["description"]}'
    data-commentaire='{$r["commentaire"]}'
>Modifier</button>

{$btnAction}
                
                <form action='index.php?page=deleteReservation' method='POST' class='form_delete_user'>
                        <input type='hidden' name='id' value='{$r["id"]}'>
                        <button class='btn btn-danger btn-sm' type='submit'><i class='fa-regular fa-trash-can' style='color: rgb(255, 255, 255);'></i>
                        </button>
                </form>
                
            </td>
        </tr>
    ";
}

/**************************************************************
 * FILTER OPTIONS
 **************************************************************/

/* ASSOCIATIONS */
$optionsAssos = "<option value=''>Toutes les associations</option>";

$stmt = $conn->query("SELECT id, nom FROM associations ORDER BY nom");
while ($asso = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $selected = (isset($_GET['association']) && $_GET['association'] == $asso['id'])
        ? "selected"
        : "";

    $optionsAssos .= "
        <option value='{$asso['id']}' $selected>
            " . htmlspecialchars($asso['nom']) . "
        </option>
    ";
}

/* TYPES */
$typeOptions = "<option value=''>Tous les types</option>";

$stmt = $conn->query("SELECT DISTINCT type FROM reservations ORDER BY type");

while ($type = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $selected = (isset($_GET['type']) && $_GET['type'] == $type['type'])
        ? "selected"
        : "";

    $typeOptions .= "
        <option value='{$type['type']}' $selected>
            " . htmlspecialchars($type['type']) . "
        </option>
    ";
}

/* SALLES */
$salleOptions = "<option value=''>Sélectionner une salle...</option>";

$stmt = $conn->query("SELECT id, nom FROM salles ORDER BY nom");

while ($salle = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $salleOptions .= "
        <option value='{$salle['id']}'>
            " . htmlspecialchars($salle['nom']) . "
        </option>
    ";
}

/**************************************************************
 * TOTAL COUNT
 **************************************************************/
$stmt = $conn->query("SELECT COUNT(*) AS total FROM reservations");
$totalReservations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];


/**************************************************************
 * ERRORS
 **************************************************************/
$errorsHTML="";
if (!empty($_SESSION["error_message"])) {
    foreach($_SESSION["error_message"] as $message) {
        if ($message) $errorsHTML.=$message;
    }
}

if ($errorsHTML) $errorsHTML="<section>".$errorsHTML."</section>";

unset($_SESSION["error_message"]);

/**************************************************************
 * TEMPLATE RENDER
 **************************************************************/
$template = file_get_contents(__DIR__ . '/../../HTML/reservation.html');

$variables = [
    "{{carteReservation}}" => $cartesHTML,
    "{{errorsHTML}}"       => $errorsHTML,
    "{{Asso_select}}"      => $optionsAssos,
    "{{Type_select}}"      => $typeOptions,
    "{{Salle_select}}"     => $salleOptions,
    "{{Reservations}}"     => $totalReservations,
    "{{personnel_menage}}" => $optionsMenage,
];

$page = str_replace(array_keys($variables), array_values($variables), $template);

echo $page;