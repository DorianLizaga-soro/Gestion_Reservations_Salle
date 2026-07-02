<?php
require_once(__DIR__ . '/connexionBDD.php');



$id_reservation        = $_POST["id_reservation"] ?? "";
$id_association        = $_POST["id_association"] ?? "";
$id_salle               = $_POST["id_salle"] ?? "";
$id_creneau_recurrent  = $_POST["id_creneau_recurrent"] ?? null;
$type                   = $_POST["type"] ?? "";
$date_                  = $_POST["date_"] ?? "";
$heure_debut            = $_POST["heure_debut"] ?? "";
$heure_fin              = $_POST["heure_fin"] ?? "";
$description            = $_POST["description"] ?? "";
$commentaire            = $_POST["commentaire"] ?? "";


$error=false;
if ($_POST) {
// Champs obligatoires (correspondent aux name="" envoyés par reservations.js)
$required = ['id_association', 'id_salle', 'type', 'date_', 'heure_debut', 'heure_fin'];

foreach ($required as $champ) {
    if (empty($_POST[$champ])) {
        $_SESSION["error_message"][]="<div class=\"alert alert-danger\">Champ manquant : $champ </div>";
        $error=true;
    }
}

if (!in_array($type, ["ponctuelle", "recurrente"], true)) {
    $_SESSION["error_message"][]='<div class="alert alert-danger">Type de réservation invalide</div>';
    $error=true;
}

if (strtotime($heure_fin) <= strtotime($heure_debut)) {
     $_SESSION["error_message"][]="<div class=\"alert alert-danger\">L'heure de fin doit être après l'heure de début</div>";
    $error=true;
}

// On récupère l'ancien programme_pdf : nécessaire pour le remplacer s'il y
// a un nouveau fichier, et pour ne pas l'effacer en base s'il n'y en a pas.
$stmt = $conn->prepare("SELECT programme_pdf FROM reservations WHERE id = ?");
$stmt->execute([$id_reservation]);
$ancienne = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ancienne) {
    header("Location: ./index.php?page=responsable&erreur=reservation_introuvable");
    exit;
}

$programme_pdf = $ancienne["programme_pdf"];

if (!empty($_FILES["programmePdf"]) && $_FILES["programmePdf"]["error"] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES["programmePdf"]["error"] !== UPLOAD_ERR_OK) {
         $_SESSION["error_message"][]="<div class=\"alert alert-danger\">Erreur lors de l'upload du fichier</div>";
        $error=true;
    }

    if ($_FILES["programmePdf"]["size"] > 10 * 1024 * 1024) {
        $_SESSION["error_message"][]='<div class="alert alert-danger">Le fichier dépasse 10 MB</div>';
        $error=true;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES["programmePdf"]["tmp_name"]);
    finfo_close($finfo);

    if ($mime !== "application/pdf") {
        $_SESSION["error_message"][]='<div class="alert alert-danger">Le fichier doit être un PDF valide</div>';
        $error=true;
    }

    $dossierUpload = "./uploads/programmes";
    if (!is_dir($dossierUpload)) {
        mkdir($dossierUpload, 0755, true);
    }

    $nomOriginal   = basename($_FILES["programmePdf"]["name"]);

    $nomFichier = $nomOriginal;

    if (!move_uploaded_file($_FILES["programmePdf"]["tmp_name"], $dossierUpload . "/" . $nomFichier)) {
         $_SESSION["error_message"][]='<div class="alert alert-danger">Impossible d\'enregistrer le fichier</div>';
       $error=true;
    }

    // L'ancien fichier n'est plus référencé une fois remplacé : on le
    // supprime pour ne pas laisser de fichiers orphelins sur le disque.
    if (!empty($ancienne["programme_pdf"])) {
        $ancienChemin = "./" . $ancienne["programme_pdf"];
        if (is_file($ancienChemin)) {
            unlink($ancienChemin);
        }
    }

    $programme_pdf = "./uploads/programmes/" . $nomFichier;
}


if ($programme_pdf) {

    $dateUpload = date('Y-m-d H:i:s');

    $stmtPdf = $conn->prepare("UPDATE pdfs SET id_reservation = ?, nom_fichier = ?, chemin = ?, date_upload = ?");

    $stmtPdf->execute([
        $id_reservation,          // réservation liée
        $nomFichier,              // nom du fichier PDF
        $programme_pdf,         // chemin complet
        $dateUpload,              // date d’upload = maintenant
    ]);
}


// Toute modification remet la réservation en attente : la salle ou les
// horaires ayant pu changer, l'ancienne validation n'est plus garantie
// pertinente.
$statut = "en_attente";

$stmt = $conn->prepare("UPDATE reservations
    SET id_association = ?, id_salle = ?, id_creneau_recurrent = ?, type = ?, date_ = ?, heure_debut = ?, heure_fin = ?, Motif = ?, commentaire = ?, programme_pdf = ?, statut = ?
    WHERE id = ?");

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
    $id_reservation,
]);
}



if (!$error) {
    unset( $_SESSION["error_message"]);
    $_SESSION["error_message"][]='<div class="alert alert-success">La réservation a été modifiée</div>';
}

header("Location:  ./index.php?page=gestionnaire");
exit;