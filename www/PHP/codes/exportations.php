<?php

require_once(__DIR__ . '/connexionBDD.php');


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



$carteSuivant = "";

foreach ($pdf as $u) {

  
        $carteSuivant .= "";
}








$variables = [
 

];


$template = file_get_contents(__DIR__ . '/../../html/exportations.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);




echo $page;
