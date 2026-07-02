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
    P.nom_fichier AS nom_fichier,
    P.id AS id,
    P.chemin AS chemin
FROM PDFs P
LEFT JOIN Reservations R ON P.id_reservation = R.id
LEFT JOIN Associations A ON R.id_association = A.id
ORDER BY P.date_upload DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$pdf = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(*) FROM PDFs";
$stmt = $conn->prepare($sql);
$stmt->execute();
$nombrePdf = $stmt->fetchColumn();


$carteEnCours = "";
$carteSuivant = "";

$nombrePdfEnCours = 0;
$nombrePdfSuivant = 0;

foreach ($pdf as $u) {

    // Si pas de PDF → on ignore
    if (empty($u["date_upload"])) {
        continue;
    }

    $triPdf = getTrimestre($u["date_upload"]);

    $nomCourt = (strlen($u["nom_fichier"]) > 20)
    ? substr($u["nom_fichier"], 0, 20) . "..."
    : $u["nom_fichier"];

    // Trimestre en cours
    if ($triPdf == $trimestreActuel) {
        $nombrePdfEnCours++;
        $carteEnCours .= "
        <div class='div_sec'>
            <div class='div_card'>
                <i class='fa-regular fa-file-pdf' style='color: red;'></i>
                <b><p>{$u["nom_association"]} - {$nomCourt} T$trimestreActuel</p></b>
                <p>{$u["nom_association"]}</p>
                <p><i class='fa-regular fa-clock'></i> {$u["date_upload"]} </p>
                <div>
                    <a href='{$u["chemin"]}' download class='btn_telecharger'>
                        <i class='fa-solid fa-download'></i> Télécharger
                    </a>



            

                    <form action='index.php?page=deletePdf' method='POST' style='display:inline;'>
                        <input type='hidden' name='id' value='{$u["id"]}'>
                        <button class='btn_delete'>
                            <i class='fa-regular fa-trash-can' style='color:red;'></i>
                        </button>
                    </form>

                </div>
            </div>
        </div>";
    }

    // Trimestre suivant
    if ($triPdf == $trimestreSuivant) {
         $nombrePdfSuivant++;
        $carteSuivant .= "
        <div class='div_sec'>
            <div class='div_card'>
                <i class='fa-regular fa-file-pdf' style='color: red;'></i>
                <b><p>{$u["nom_association"]} - {$nomCourt} T$trimestreSuivant</p></b>
                <p>{$u["nom_association"]}</p>
                <p><i class='fa-regular fa-clock'></i> {$u["date_upload"]}</p>
                <div>
                <a href='{$u["chemin"]}' download class='btn_telecharger'>
    <i class='fa-solid fa-download'></i> Télécharger
</a>


                    <form action='index.php?page=deletePdf' method='POST' style='display:inline;'>
    <input type='hidden' name='id' value='{$u["id"]}'>
    <button class='btn_delete'>
        <i class='fa-regular fa-trash-can' style='color:red;'></i>
    </button>
</form>

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
    "{{nombrePdfTe}}" => $nombrePdfEnCours,
    "{{nombrePdfTs}}" => $nombrePdfSuivant



];


$template = file_get_contents(__DIR__ . '/../../HTML/panneaudaffichage.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);




echo $page;