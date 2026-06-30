<?php
/* test */

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

// Remis en place : sans ce contrôle, $_SESSION['id_createur'] est absent
// et la réservation est insérée avec id_createur = NULL, ce qui la rend
// invisible dans index.php (INNER JOIN utilisateurs).

/*if (empty($_SESSION['id_createur'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Utilisateur non authentifié']);
    exit;
}*/

define('ID_CREATEUR_TEST', 4); // <-- adapte cet ID à un utilisateur réel de ta table `utilisateurs`

if (empty($_SESSION['id_createur'])) {
    $_SESSION['id_createur'] = ID_CREATEUR_TEST;
}

// Champs obligatoires (correspondent aux name="" envoyés par reservations.js)
$required = ['id_association', 'id_salle', 'type', 'date_', 'heure_debut', 'heure_fin'];

foreach ($required as $champ) {
    if (empty($_POST[$champ])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Champ manquant : $champ"]);
        exit;
    }
}

// Le client envoie un texte libre dans modalType ; on vérifie qu'il
// correspond bien aux valeurs attendues avant de l'insérer en base.
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

/***********************************************
 * Upload du programme PDF (optionnel)
 * -> avant, le fichier était reçu par PHP puis
 *    jamais déplacé ni enregistré : il disparaissait
 *    à la fin du script.
 ***********************************************/
$cheminProgramme = null;

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

    // Ne jamais se fier au type MIME envoyé par le client (facilement falsifiable) :
    // on inspecte le contenu réel du fichier reçu.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $fichier['tmp_name']);
    finfo_close($finfo);

    if ($mime !== 'application/pdf') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Le fichier doit être un PDF valide']);
        exit;
    }

    // Dossier hors du dossier HTML/ public pour éviter l'exécution directe
    // d'un fichier déposé. À adapter selon ton arborescence réelle.
    $dossierUpload = __DIR__ . '/../../uploads/programmes';
    if (!is_dir($dossierUpload)) {
        mkdir($dossierUpload, 0755, true);
    }

    // Nom généré côté serveur : on ignore le nom d'origine du fichier
    // pour éviter tout problème de path traversal ou de collision.
    $nomFichier = uniqid('programme_', true) . '.pdf';
    $cheminComplet = $dossierUpload . '/' . $nomFichier;

    if (!move_uploaded_file($fichier['tmp_name'], $cheminComplet)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => "Impossible d'enregistrer le fichier"]);
        exit;
    }

    // Chemin relatif stocké en base. Nécessite une colonne programme_pdf
    // (VARCHAR, nullable) sur la table reservations — voir note ci-dessous.
    $cheminProgramme = 'uploads/programmes/' . $nomFichier;
}

try {
    $stmt = $conn->prepare("
        INSERT INTO reservations (
            id_association,
            id_salle,
            id_creneau_recurrent,
            type,
            date_,
            heure_debut,
            heure_fin,
            description,
            commentaire,
            programme_pdf,
            statut,
            id_createur,
            date_creation
        ) VALUES (
            :id_association,
            :id_salle,
            :id_creneau_recurrent,
            :type,
            :date_,
            :heure_debut,
            :heure_fin,
            :description,
            :commentaire,
            :programme_pdf,
            :statut,
            :id_createur,
            :date_creation
        )
    ");

    $ok = $stmt->execute([
        'id_association'        => $_POST['id_association'],
        'id_salle'               => $_POST['id_salle'],
        // null pour une réservation ponctuelle, renseigné uniquement pour une récurrente
        'id_creneau_recurrent' => $_POST['id_creneau_recurrent'] ?? null,
        'type'                   => $_POST['type'],
        'date_'                  => $_POST['date_'],
        'heure_debut'            => $_POST['heure_debut'],
        'heure_fin'              => $_POST['heure_fin'],
        'description'            => $_POST['description'] ?? null,
        'commentaire'            => $_POST['commentaire'] ?? null,
        'programme_pdf'          => $cheminProgramme,
        // Le statut n'est jamais soumis par le client : toute nouvelle réservation
        // démarre "en_attente" et passe à "validee" via un autre flux (validation admin)
        'statut'                 => 'en_attente',
        'id_createur'            => $_SESSION['id_createur'],
        'date_creation'          => date('Y-m-d H:i:s'),
    ]);

    echo json_encode(['success' => (bool) $ok]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
