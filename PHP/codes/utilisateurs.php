<?php

require_once(__DIR__ . '/connexionBDD.php');


$sql = "SELECT 
    U.*, 
    A.nom AS nom_association
FROM Utilisateurs U
LEFT JOIN Associations A ON U.id_association = A.id
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT COUNT(*) FROM Utilisateurs WHERE role = 'gestionnaire'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$nombreGestionnaire = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) FROM Utilisateurs WHERE role = 'responsable_association'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$nombreResponsable = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) FROM Utilisateurs WHERE role = 'membre_association'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$nombreMembre = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) FROM Utilisateurs WHERE role = 'personnel_menage'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$nombreMenage = $stmt->fetchColumn();

$sql = "SELECT COUNT(*) FROM Utilisateurs";
$stmt = $conn->prepare($sql);
$stmt->execute();
$nombreUtilisateur = $stmt->fetchColumn();

$cartesHTML = "";


foreach ($utilisateurs as $u) {

 
    // Carte
    $cartesHTML .= "
    <div class='div_userElement'>
                <div class='div_user'>
                    <span class='initial_resp'>" . substr($u["nom"], 0, 2) . "</span>   
                    <p>{$u["nom"]}</p> 
                </div>
                <p>{$u["email"]}</p>
                <p>{$u["nom_association"]}</p>
                <p>{$u["role"]}</p>

                <div class='btn_modifDelete'>
                    <button class='btn_modif'
    data-id='{$u["id"]}'
    data-nom='{$u["nom"]}'
    data-email='{$u["email"]}'
    data-password='{$u["password"]}'
    data-role='{$u["role"]}'
    data-association='{$u["id_association"]}'
>
    <i class='fa-regular fa-pen-to-square' style='color: rgb(148, 148, 148);'></i>
</button>

                    <form action='/PHP/codes/deleteUser.php' method='POST' class='form_delete_user'>
                        <input type='hidden' name='id_user' value='{$u["id"]}'>
                        <button class='btn_delete' type='submit'>
                            <i class='fa-regular fa-trash-can' style='color: rgb(255, 0, 0);'></i>
                        </button>
                    </form>

                </div>
            </div>
    ";
}



$sql = "SELECT id, nom FROM Associations";
$stmt = $conn->prepare($sql);
$stmt->execute();
$associations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$optionsAssos = "";
foreach ($associations as $a) {
    $optionsAssos .= "<option value='{$a["id"]}'>{$a["nom"]}</option>";
}


$messageErreur = "";

if (isset($_GET["error"])) {
    if ($_GET["error"] === "responsable") {
        $messageErreur = "<div class='alert alert-error' style='color:red'>Impossible de supprimer cet utilisateur : il est responsable d'une association.</div>";
       
    }
    if ($_GET["error"] === "missing_id") {
        $messageErreur = "<div class='alert alert-error'>Erreur : ID utilisateur manquant.</div>";
        
    }
    
}

if (isset($_GET["success"]) && $_GET["success"] === "deleted") {
    $messageErreur = "<div class='alert alert-success' style='color:red'>Utilisateur supprimé avec succès.</div>";
}

$variables = [
    "{{carteUtilisateur}}"=> $cartesHTML,
    "{{nombreGestionnaire}}" => $nombreGestionnaire,
    "{{nombreResponsable}}"=>$nombreResponsable,
    "{{nombreMembre}}"=>$nombreMembre,
    "{{nombreMenage}}"=>$nombreMenage,
    "{{nombreUtilisateur}}"=>$nombreUtilisateur,
    "{{optionsAssociations}}" => $optionsAssos,
    "{{messageErreur}}" => $messageErreur,

];


$template = file_get_contents(__DIR__ . '/../../html/utilisateur.html');


$page = str_replace(array_keys($variables), array_values($variables), $template);




echo $page;
