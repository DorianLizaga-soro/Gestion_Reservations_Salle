<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/connexionBDD.php');

// Remis en place : sans ce contrôle, $_SESSION['id_createur'] est absent
// et la réservation est insérée avec id_createur = NULL, ce qui la rend
// invisible dans index.php (INNER JOIN utilisateurs).




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

// Le client envoie un texte libre dans modalType ; on vérifie qu'il
// correspond bien aux valeurs attendues avant de l'insérer en base.
if (!in_array($_POST['type'], ['ponctuelle', 'recurrente'], true)) {
    $_SESSION["error_message"][]='<div class="alert alert-danger">Type de réservation invalide</div>';
    $error=true;
    
}

if (strtotime($_POST['heure_fin']) <= strtotime($_POST['heure_debut'])) {
    $_SESSION["error_message"][]="<div class=\"alert alert-danger\">L'heure de fin doit être après l'heure de début</div>";
    $error=true;
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
        $_SESSION["error_message"][]="<div class=\"alert alert-danger\">Erreur lors de l'upload du fichier</div>";
        $error=true;
       
    }

    $maxBytes = 10 * 1024 * 1024;
    if ($fichier['size'] > $maxBytes) {
        $_SESSION["error_message"][]='<div class="alert alert-danger">Le fichier dépasse 10 MB</div>';
        $error=true;
        
    }

    // Ne jamais se fier au type MIME envoyé par le client (facilement falsifiable) :
    // on inspecte le contenu réel du fichier reçu.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $fichier['tmp_name']);
    finfo_close($finfo);

    if ($mime !== 'application/pdf') {
        $_SESSION["error_message"][]='<div class="alert alert-danger">Le fichier doit être un PDF valide</div>';
        $error=true;
        
    }

     if ($error) {
        header("Location: ./index.php?page=gestionnaire");
        exit;
    }
    // Dossier hors du dossier HTML/ public pour éviter l'exécution directe
    // d'un fichier déposé. À adapter selon ton arborescence réelle.
    $dossierUpload ='./uploads/programmes';
    if (!is_dir($dossierUpload)) {
        mkdir($dossierUpload, 0755, true);
    }

    // Nom généré côté serveur : on ignore le nom d'origine du fichier
    // pour éviter tout problème de path traversal ou de collision.
    $nomOriginal   = basename($_FILES["programmePdf"]["name"]);
    $nomFichier = $nomOriginal;
    $cheminComplet = $dossierUpload . '/' . $nomFichier;

    if (!move_uploaded_file($fichier['tmp_name'], $cheminComplet)) {
       $_SESSION["error_message"][]='<div class="alert alert-danger">Impossible d\'enregistrer le fichier</div>';
       $error=true;
        
    }

    // Chemin relatif stocké en base. Nécessite une colonne programme_pdf
    // (VARCHAR, nullable) sur la table reservations — voir note ci-dessous.
    $cheminProgramme = './uploads/programmes/' . $nomFichier;
}

if (!$error) {
    $stmt = $conn->prepare("
        INSERT INTO reservations (
            id_association,
            id_salle,
            id_creneau_recurrent,
            type,
            date_,
            heure_debut,
            heure_fin,
            Motif,
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
            :Motif,
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
        'Motif'            => $_POST['description'] ?? null,
        'commentaire'            => $_POST['commentaire'] ?? null,
        'programme_pdf'          => $cheminProgramme,
        // Le statut n'est jamais soumis par le client : toute nouvelle réservation
        // démarre "en_attente" et passe à "validee" via un autre flux (validation admin)
         'statut'                 => 'en_attente',
        'id_createur'            => $_SESSION['id'],
        'date_creation'          => date('Y-m-d H:i:s'),
    ]);



        if ($ok)  {
            unset( $_SESSION["error_message"]);
            $_SESSION["error_message"][]='<div class="alert alert-success">La réservation a été créée</div>';
        }

        // 2) Récupérer l'ID de la réservation
$id_reservation = $conn->lastInsertId();
$dateUpload = date('Y-m-d H:i:s');
$dateExpiration = date('Y-m-d H:i:s', strtotime('+3 months'));


if ($cheminProgramme) {

    $dateUpload = date('Y-m-d H:i:s');
    $dateExpiration = date('Y-m-d H:i:s', strtotime('+3 months'));

    $stmtPdf = $conn->prepare("
        INSERT INTO pdfs (
            id_reservation,
            nom_fichier,
            chemin,
            date_upload,
            date_expiration
        ) VALUES (?, ?, ?, ?, ?)
    ");

    $stmtPdf->execute([
        $id_reservation,          // réservation liée
        $nomFichier,              // nom du fichier PDF
        $cheminProgramme,         // chemin complet
        $dateUpload,              // date d’upload = maintenant
        $dateExpiration           // expiration = +3 mois
    ]);
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

// Récupération des valeurs nécessaires
$id_menage = $_POST['id_menage'] ?? null;
$date_ = $_POST['date_'] ?? null;

// Vérifications
if (!$id_menage) {
    $_SESSION["error_message"][] = '<div class="alert alert-danger">Personnel ménage manquant</div>';
    header("Location: ./index.php?page=gestionnaire");
    exit;
}

if (!$date_) {
    $_SESSION["error_message"][] = '<div class="alert alert-danger">Date manquante pour le ménage</div>';
    header("Location: ./index.php?page=gestionnaire");
    exit;
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
    }
    
} 

header("location:./index.php?page=gestionnaire");
?>