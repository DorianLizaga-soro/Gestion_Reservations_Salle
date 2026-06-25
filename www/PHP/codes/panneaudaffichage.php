<?php

require_once(__DIR__ . '/connexionBDD.php');

function getTrimestre($date) {
    $month = (int)date("m", strtotime($date));
    return ceil($month / 3); 
}

$trimestreActuel = getTrimestre(date("Y-m-d"));
$trimestreSuivant = $trimestreActuel + 1;
if ($trimestreSuivant > 4) $trimestreSuivant = 1;

$sql = "SELECT 
    A.nom AS nom_association,
    P.date_upload AS date_upload,
    P.nom_fichier AS nom_fichier
FROM Utilisateurs U
LEFT JOIN Associations A ON U.id_association = A.id
LEFT JOIN Reservations R ON A.id = R.id_association
LEFT JOIN PDFs P ON R.id = P.id_reservation
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$pdf = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(*) FROM PDFs";
$stmt = $conn->prepare($sql);
$stmt->execute();
$nombrePdf = $stmt->fetchColumn();


$carteEnCours = "";
$carteSuivant = "";

foreach ($pdf as $u) {

    // Si pas de PDF → on ignore
    if (empty($u["date_upload"])) {
        continue;
    }

    $triPdf = getTrimestre($u["date_upload"]);

    // Trimestre en cours
    if ($triPdf == $trimestreActuel) {
        $carteEnCours .= "
        <div class='div_sec'>
            <div class='div_card'>
                <i class='fa-regular fa-file-pdf' style='color: red;'></i>
                <b><p>{$u["nom_association"]} - {$u["nom_fichier"]} T$trimestreActuel</p></b>
                <p>{$u["nom_association"]}</p>
                <p><i class='fa-regular fa-clock'></i> {$u["date_upload"]} </p>
                <div>
                    <button class='btn_telecharger'><i class='fa-solid fa-download'></i> Télécharger</button>
                    <button class='btn_delete'><i class='fa-regular fa-trash-can' style='color: red;'></i></button>
                </div>
            </div>
        </div>";
    }

    // Trimestre suivant
    if ($triPdf == $trimestreSuivant) {
        $carteSuivant .= "
        <div class='div_sec'>
            <div class='div_card'>
                <i class='fa-regular fa-file-pdf' style='color: red;'></i>
                <b><p>{$u["nom_association"]} - {$u["nom_fichier"]} T$trimestreSuivant</p></b>
                <p>{$u["nom_association"]}</p>
                <p><i class='fa-regular fa-clock'></i> {$u["date_upload"]}</p>
                <div>
                    <button class='btn_telecharger'><i class='fa-solid fa-download'></i> Télécharger</button>
                    <button class='btn_delete'><i class='fa-regular fa-trash-can' style='color: red;'></i></button>
                </div>
            </div>
        </div>";
    }
}







$variables = [
    "{{pdfEnCours}}"=> $carteEnCours,
    "{{pdfSuivant}}"=>$carteSuivant,
    "{{trimestreencours}}"=>$trimestreActuel,
    "{{trimestresuivant}}"=>$trimestreSuivant,
    "{{annee}}"=>date("Y"),
    "{{nombrePdf}}"=>$nombrePdf

];


$template = file_get_contents(__DIR__ . '/../../html/panneaudaffichage.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);




echo $page;
