<?php

require_once(__DIR__ . '/connexionBDD.php');

define('ID_CREATEUR_TEST', 4); // <-- adapte cet ID à un utilisateur réel de ta table `utilisateurs`

if (empty($_SESSION['id_createur'])) {
    $_SESSION['id_createur'] = ID_CREATEUR_TEST;
}

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
        reservations.description AS description,
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

    $cartesHTML .= "
        <tr>
            <td>{$r["date_"]}</td>
            <td>{$r["heure_debut"]} - {$r["heure_fin"]}</td>
            <td>{$r["nomAssos"]}</td>
            <td>{$r["type"]}</td>
            <td>{$r["Salle"]}</td>
            <td>{$r["nomResponsable"]}</td>
            <td>{$badge}</td>
            <td>
                <button class='btn btn-secondary btn-sm' data-id='{$r['id']}' onClick=\"remplirModale(".$r["id_association"].",".$r["id_salle"].",'".$r["type"]."','".$r["date_"]."','".$r["heure_debut"]."','".$r["heure_fin"]."','".$r["description"]."','".$r["commentaire"]."');showModal(true)\">Voir</button>
                <button class='btn btn-secondary btn-sm' data-id='{$r['id']}' onClick=\"remplirModale(".$r["id_association"].",".$r["id_salle"].",'".$r["type"]."','".$r["date_"]."','".$r["heure_debut"]."','".$r["heure_fin"]."','".$r["description"]."','".$r["commentaire"]."');showModal(false);changeButtonTitle()\">Modifier</button>
                <button class='btn btn-danger btn-sm' data-id='{$r['id']}'>Supprimer</button>
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
];

$page = str_replace(array_keys($variables), array_values($variables), $template);

echo $page;