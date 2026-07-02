<?php
require_once(__DIR__ . '/connexionBDD.php');

if (!isset($_SESSION["id"])) {
    header("Location: ./index.php?page=login");
    exit;
}

$id_reservation = $_POST["id_reservation"];
$id_salle       = $_POST["id_salle"];
$date_          = $_POST["date_"];
$heure_debut    = $_POST["heure_debut"];
$heure_fin      = $_POST["heure_fin"];
$motif          = $_POST["Motif"] ?? "";
$commentaire    = $_POST["commentaire"] ?? "";

// 1) UPDATE réservation
$stmt = $conn->prepare("
    UPDATE reservations 
    SET id_salle = ?, date_ = ?, heure_debut = ?, heure_fin = ?, Motif = ?, commentaire = ?
    WHERE id = ?
");
$stmt->execute([$id_salle, $date_, $heure_debut, $heure_fin, $motif, $commentaire, $id_reservation]);


// 2) UPDATE ménage
$id_menage = $_POST["id_menage"];

$stmtCheck = $conn->prepare("SELECT id FROM menage WHERE id_reservation = ?");
$stmtCheck->execute([$id_reservation]);
$menageId = $stmtCheck->fetchColumn();

if ($menageId) {
    $stmtUpdate = $conn->prepare("UPDATE menage SET id_personnel = ? WHERE id_reservation = ?");
    $stmtUpdate->execute([$id_menage, $id_reservation]);
} else {
    $stmtInsert = $conn->prepare("
        INSERT INTO menage (id_reservation, id_personnel, date_prevue, statut)
        VALUES (?, ?, NOW(), 'attente')
    ");
    $stmtInsert->execute([$id_reservation, $id_menage]);
}

// 3) UPDATE programme PDF
if (isset($_FILES["programmePdfEdit"]) && $_FILES["programmePdfEdit"]["error"] === 0) {

    $nomOriginal = basename($_FILES["programmePdfEdit"]["name"]);
    $extension   = pathinfo($nomOriginal, PATHINFO_EXTENSION);
    $nomFichier  = $nomOriginal;

    $dossier       = './uploads/programmes/';
    $cheminServeur = $dossier . $nomFichier;
    $cheminBDD     = 'uploads/programmes/' . $nomFichier;

    if (!is_dir($dossier)) {
        mkdir($dossier, 0777, true);
    }

    if (move_uploaded_file($_FILES["programmePdfEdit"]["tmp_name"], $cheminServeur)) {

        // UPDATE réservation → programme_pdf
        $stmtUpdatePdf = $conn->prepare("
            UPDATE reservations 
            SET programme_pdf = ?
            WHERE id = ?
        ");
        $stmtUpdatePdf->execute([$nomFichier, $id_reservation]);

        // UPDATE ou INSERT dans pdfs
        $stmtPdf = $conn->prepare("
            INSERT INTO pdfs (id_reservation, nom_fichier, chemin)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                nom_fichier = VALUES(nom_fichier),
                chemin = VALUES(chemin)
        ");
        $stmtPdf->execute([$id_reservation, $nomOriginal, $cheminBDD]);
    }
}



header("Location: ./index.php?page=responsable");
exit;
