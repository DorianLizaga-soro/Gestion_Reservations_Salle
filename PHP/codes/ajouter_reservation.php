<?php

require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: ./index.php?page=login");
    exit;
}

$id_association = $_SESSION["id_association"];
$id_createur    = $_SESSION["id"];
$id_salle       = $_POST["id_salle"];
$date_          = $_POST["date_"];
$heure_debut    = $_POST["heure_debut"];
$heure_fin      = $_POST["heure_fin"];
$motif          = $_POST["Motif"] ?? "";
$commentaire    = $_POST["commentaire"] ?? "";
$id_menage = $_POST["id_menage"];






// INSERT réservation
$stmt = $conn->prepare("
    INSERT INTO reservations 
    (id_association, id_salle, type, date_, heure_debut, heure_fin, statut, id_createur, Motif, commentaire, programme_pdf) 
    VALUES (?, ?, 'ponctuelle', ?, ?, ?, 'en_attente', ?, ?, ?, NULL)
");

$stmt->execute([
    $id_association,
    $id_salle,
    $date_,
    $heure_debut,
    $heure_fin,
    $id_createur,
    $motif,
    $commentaire
]);

$id_reservation = $conn->lastInsertId();


// UPLOAD PDF
if (isset($_FILES["programmePdf"]) && $_FILES["programmePdf"]["error"] === 0) {

    $nomOriginal = basename($_FILES["programmePdf"]["name"]);
    $extension   = pathinfo($nomOriginal, PATHINFO_EXTENSION);
    $nomFichier  = $nomOriginal;

    $dossier       = './uploads/programmes/';
    $cheminServeur = $dossier . $nomFichier;
    $cheminBDD     = 'uploads/programmes/' . $nomFichier;

    if (!is_dir($dossier)) {
        mkdir($dossier, 0777, true);
    }

    if (move_uploaded_file($_FILES["programmePdf"]["tmp_name"], $cheminServeur)) {

        // UPDATE réservation → on met le nom du PDF
        $stmtUpdate = $conn->prepare("
            UPDATE reservations 
            SET programme_pdf = ?
            WHERE id = ?
        ");
        $stmtUpdate->execute([$nomFichier, $id_reservation]);

        // INSERT dans pdfs
        $stmtPdf = $conn->prepare("
            INSERT INTO pdfs (id_reservation, nom_fichier, chemin, date_upload, date_expiration)
            VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH))
        ");
        $stmtPdf->execute([$id_reservation, $nomOriginal, $cheminBDD]);
    }
}


if (!empty($_POST['commentaire'])) {

    $stmtCom = $conn->prepare("
        INSERT INTO commentaires (id_reservation, id_auteur, contenu, date_creation)
        VALUES (:id_reservation, :id_auteur, :contenu, :date_creation)
    ");

    $stmtCom->execute([
        'id_reservation'  => $id_reservation,
        'id_auteur'  => $_SESSION['id'],   // auteur du commentaire
        'contenu'     => $_POST['commentaire'],
        'date_creation'=> date('Y-m-d H:i:s')
    ]);
}



// 3) INSERT dans menage
$stmt = $conn->prepare("
    INSERT INTO menage 
    (id_reservation, id_personnel, date_prevue, date_validation, statut)
    VALUES (?, ?, ?, ?, 'attente')
");

$stmt->execute([
    $id_reservation,
    $id_menage,
    $date_,      // ménage prévu le jour de la réservation
    null         // pas encore validé
]);




header("Location: ./index.php?page=responsable");
exit;
