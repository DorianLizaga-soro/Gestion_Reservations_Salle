<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/connexionBDD.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Même béquille que dans creerReservation.php tant que l'authentification
// réelle n'est pas branchée — à retirer en même temps que l'autre.
define('ID_CREATEUR_TEST', 1);

if (empty($_SESSION['id_createur'])) {
    $_SESSION['id_createur'] = ID_CREATEUR_TEST;
}

// id_reservation est posté (et non passé en GET) pour rester cohérent
// avec le reste du formulaire, qui poste déjà tous les autres champs.
$required = ['id_reservation', 'id_association', 'id_salle', 'type', 'date_', 'heure_debut', 'heure_fin'];

foreach ($required as $champ) {
    if (empty($_POST[$champ])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Champ manquant : $champ"]);
        exit;
    }
}

$id = $_POST['id_reservation'];

if (!in_array($_POST['type'], ['ponctuelle', 'recurrente'], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Type de réservation invalide']);
    exit;
}

if (strtotime($_POST['heure_fin']) <= strtotime($_POST['heure_debut'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => "L'heure de fin doit être après l'heure de début"]);
    exit;
}

try {
    // On vérifie l'existence avant toute écriture, et on récupère l'ancien
    // programme_pdf pour pouvoir le supprimer du disque s'il est remplacé.
    $stmt = $conn->prepare("SELECT programme_pdf FROM reservations WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $existante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existante) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Réservation introuvable']);
        exit;
    }

    $cheminProgramme = $existante['programme_pdf'];

    /***********************************************
     * Remplacement éventuel du programme PDF
     * (logique identique à creerReservation.php)
     ***********************************************/
    if (!empty($_FILES['programmePdf']) && $_FILES['programmePdf']['error'] !== UPLOAD_ERR_NO_FILE) {

        $fichier = $_FILES['programmePdf'];

        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Erreur lors de l'upload du fichier"]);
            exit;
        }

        $maxBytes = 10 * 1024 * 1024;
        if ($fichier['size'] > $maxBytes) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Le fichier dépasse 10 MB']);
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fichier['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'application/pdf') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Le fichier doit être un PDF valide']);
            exit;
        }

        $dossierUpload = __DIR__ . '/../../uploads/programmes';
        if (!is_dir($dossierUpload)) {
            mkdir($dossierUpload, 0755, true);
        }

        $nomFichier = uniqid('programme_', true) . '.pdf';
        $cheminComplet = $dossierUpload . '/' . $nomFichier;

        if (!move_uploaded_file($fichier['tmp_name'], $cheminComplet)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => "Impossible d'enregistrer le fichier"]);
            exit;
        }

        // L'ancien fichier n'est plus référencé par aucune réservation une
        // fois remplacé : on le supprime pour ne pas laisser de fichiers
        // orphelins s'accumuler sur le disque.
        if (!empty($existante['programme_pdf'])) {
            $ancienChemin = __DIR__ . '/../../' . $existante['programme_pdf'];
            if (is_file($ancienChemin)) {
                unlink($ancienChemin);
            }
        }

        $cheminProgramme = 'uploads/programmes/' . $nomFichier;
    }

    $stmt = $conn->prepare("
        UPDATE reservations SET
            id_association        = :id_association,
            id_salle               = :id_salle,
            id_creneau_recurrent  = :id_creneau_recurrent,
            type                   = :type,
            date_                  = :date_,
            heure_debut            = :heure_debut,
            heure_fin              = :heure_fin,
            description            = :description,
            commentaire            = :commentaire,
            programme_pdf          = :programme_pdf,
            statut                 = :statut
        WHERE id = :id
    ");

    $ok = $stmt->execute([
        'id_association'        => $_POST['id_association'],
        'id_salle'               => $_POST['id_salle'],
        'id_creneau_recurrent' => $_POST['id_creneau_recurrent'] ?? null,
        'type'                   => $_POST['type'],
        'date_'                  => $_POST['date_'],
        'heure_debut'            => $_POST['heure_debut'],
        'heure_fin'              => $_POST['heure_fin'],
        'description'            => $_POST['description'] ?? null,
        'commentaire'            => $_POST['commentaire'] ?? null,
        'programme_pdf'          => $cheminProgramme,
        // Toute modification remet la réservation "en_attente" : la salle
        // ou les horaires ayant pu changer, l'ancienne validation n'est
        // plus garantie pertinente et doit être revue.
        'statut'                 => 'en_attente',
        'id'                     => $id,
    ]);

    echo json_encode(['success' => (bool) $ok]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
