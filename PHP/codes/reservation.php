<?php

require_once(__DIR__ . '/connexionBDD.php');

/***********************************************
 * Création d'une réservation
 * -> gérée exclusivement par creerReservation.php
 *    (plus de logique d'insertion dupliquée ici)
 ***********************************************/

/***********************************************
 * Filtres (GET)
 ***********************************************/
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

$sql = "
    SELECT
        reservations.id,
        date_,
        heure_debut,
        heure_fin,
        associations.nom AS nomAssos,
        type,
        salles.nom AS Salle,
        statut,
        utilisateurs.nom AS nomResponsable
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



/***********************************************
 * Construction des lignes du tableau
 * (utilise désormais le résultat filtré ci-dessus,
 *  et plus une requête séparée non filtrée)
 ***********************************************/
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
                <button class='btn btn-secondary btn-sm' data-id='{$r['id']}'>Voir</button>
                <button class='btn btn-secondary btn-sm' data-id='{$r['id']}'>Modifier</button>
                <button class='btn btn-danger btn-sm' data-id='{$r['id']}'>Supprimer</button>
            </td>
          </tr>
    ";
    
}

$sql = "SELECT id, nom FROM Associations";
$stmt = $conn->prepare($sql);
$stmt->execute();
$associations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$optionsAssos = "";
foreach ($associations as $a) {
    $optionsAssos .= "<option value='{$a["nom"]}'>{$a["nom"]}</option>";
}


/***********************************************
 * Options des filtres / de la modale
 ***********************************************/
$optionsAssos = "<option value=''>Toutes les associations</option>";

$stmt = $conn->query("SELECT id, nom FROM associations ORDER BY nom");
while ($asso = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $selected = (isset($_GET['association']) && $_GET['association'] == $asso['id']) ? 'selected' : '';
    $optionsAssos .= "<option value='{$asso['nom']}' $selected>" . htmlspecialchars($asso['nom']) . "</option>";
}

$typeOptions = "<option value=''>Tous les types</option>";

$stmt = $conn->query("SELECT DISTINCT type FROM reservations ORDER BY type");
while ($type = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $selected = (isset($_GET['type']) && $_GET['type'] == $type['type']) ? 'selected' : '';
    $typeOptions .= "<option value='{$type['type']}' $selected>" . htmlspecialchars($type['type']) . "</option>";
}








// Liste des salles générée dynamiquement (au lieu d'être codée en dur dans le HTML)
$salleOptions = "<option value=''>Sélectionner une salle...</option>";

$stmt = $conn->query("SELECT id, nom FROM salles ORDER BY nom");
while ($salle = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $salleOptions .= "<option value='{$salle['id']}'>" . htmlspecialchars($salle['nom']) . "</option>";
}

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM reservations");
$stmt->execute();

$totalReservations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$variables = [
    "{{carteReservation}}" => $cartesHTML,
    "{{Asso_select}}"      => $optionsAssos,
    "{{Type_select}}"      => $typeOptions,
    "{{Salle_select}}"     => $salleOptions,
    "{{Reservations}}"     => $totalReservations,
];

$template = file_get_contents(__DIR__ . '/../../HTML/reservation.html');
$page = str_replace(array_keys($variables), array_values($variables), $template);

echo $page;
