<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: /ReservationSalle/index.php?page=login");
    exit;
}

$id_association        = $_POST["id_association"] ?? "";
$id_salle               = $_POST["id_salle"] ?? "";
$id_creneau_recurrent  = $_POST["id_creneau_recurrent"] ?? null;
$type                   = $_POST["type"] ?? "";
$date_                  = $_POST["date_"] ?? "";
$heure_debut            = $_POST["heure_debut"] ?? "";
$heure_fin              = $_POST["heure_fin"] ?? "";
$description            = $_POST["description"] ?? "";
$commentaire            = $_POST["commentaire"] ?? "";

if (
    $id_association === "" ||
    $id_salle === "" ||
    $type === "" ||
    $date_ === "" ||
    $heure_debut === "" ||
    $heure_fin === ""
) {
    header("Location: /ReservationSalle/index.php?page=responsable&erreur=champs_manquants");
    exit;
}

if (!in_array($type, ["ponctuelle", "recurrente"], true)) {
    header("Location: /ReservationSalle/index.php?page=responsable&erreur=type_invalide");
    exit;
}

if (strtotime($heure_fin) <= strtotime($heure_debut)) {
    header("Location: /ReservationSalle/index.php?page=responsable&erreur=horaire_invalide");
    exit;
}

/***********************************************
 * Upload du programme PDF (optionnel)
 ***********************************************/
$programme_pdf = null;

if (!empty($_FILES["programmePdf"]) && $_FILES["programmePdf"]["error"] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES["programmePdf"]["error"] !== UPLOAD_ERR_OK) {
        header("Location: /ReservationSalle/index.php?page=responsable&erreur=upload_echoue");
        exit;
    }

    if ($_FILES["programmePdf"]["size"] > 10 * 1024 * 1024) {
        header("Location: /ReservationSalle/index.php?page=responsable&erreur=fichier_trop_lourd");
        exit;
    }

    // Ne jamais se fier au type MIME envoyé par le client : on inspecte
    // le contenu réel du fichier reçu.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES["programmePdf"]["tmp_name"]);
    finfo_close($finfo);

    if ($mime !== "application/pdf") {
        header("Location: /ReservationSalle/index.php?page=responsable&erreur=fichier_invalide");
        exit;
    }

    $dossierUpload = __DIR__ . "/../../uploads/programmes";
    if (!is_dir($dossierUpload)) {
        mkdir($dossierUpload, 0755, true);
    }

    // Nom généré côté serveur : on ignore le nom d'origine pour éviter
    // tout path traversal ou collision.
    $nomFichier = uniqid("programme_", true) . ".pdf";

    if (!move_uploaded_file($_FILES["programmePdf"]["tmp_name"], $dossierUpload . "/" . $nomFichier)) {
        header("Location: /ReservationSalle/index.php?page=responsable&erreur=enregistrement_echoue");
        exit;
    }

    $programme_pdf = "uploads/programmes/" . $nomFichier;
}

// Le statut et l'id du créateur ne sont jamais pris dans $_POST :
// statut démarre toujours "en_attente", et le créateur vient de la
// session, jamais d'un champ que le client pourrait falsifier.
$statut        = "en_attente";
$id_createur   = $_SESSION["id"];
$date_creation = date("Y-m-d H:i:s");

$stmt = $conn->prepare("INSERT INTO reservations
    (id_association, id_salle, id_creneau_recurrent, type, date_, heure_debut, heure_fin, description, commentaire, programme_pdf, statut, id_createur, date_creation)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
    $id_association,
    $id_salle,
    $id_creneau_recurrent,
    $type,
    $date_,
    $heure_debut,
    $heure_fin,
    $description,
    $commentaire,
    $programme_pdf,
    $statut,
    $id_createur,
    $date_creation,
]);

header("Location: /ReservationSalle/index.php?page=responsable");
exit;
