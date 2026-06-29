<?php
ob_start();
session_start();

require_once __DIR__ . '/PHP/codes/connexionBDD.php';

$page = $_GET['page'] ?? 'login';

switch($page){
    case 'login':
        include __DIR__ . '/PHP/codes/login.php';
        break;
    
    case 'register':
        include __DIR__ . '/PHP/codes/register.php';
        break;
    
    case 'menage':
        include __DIR__ . '/PHP/codes/menage.php';
        break;

    case 'responsable':
        include __DIR__ . '/PHP/codes/responsable.php';
        break;
    
    case 'membre':
        include __DIR__ . '/PHP/codes/membre.php';
        break;
    
    case 'gestionnaire':
        include __DIR__ . '/PHP/codes/gestionnaire.php';
        break;

    case 'ajouter_reservation':
        include __DIR__ . '/PHP/codes/ajouter_reservation.php';
        break;

    case 'supprimer_reservation':
        include __DIR__ . '/PHP/codes/supprimer_reservation.php';
        break;

    case 'modifier_reservation':
        include __DIR__ . '/PHP/codes/modifier_reservation.php';
        break;

    case 'ajouter_membre':
        include __DIR__ . '/PHP/codes/ajouter_membre.php';
        break;

    case 'ajouter_fichier':
        include __DIR__ . '/PHP/codes/ajouter_fichier.php';
        break;

    case 'deconnexion':
        include __DIR__ . '/PHP/codes/logout.php';
        break;

    case 'export_excel':
        include __DIR__ . '/PHP/codes/export_excel.php';
        break;

    case 'export_calendar':
    include __DIR__ . '/PHP/codes/export_calendar.php';
        break;    

    case 'associationAdmin':
        include __DIR__ . '/PHP/codes/associationAdmin.php';
        break; 

    case 'get_association_details':
        include __DIR__ . '/PHP/codes/get_association_details.php';
        break;

    case 'supprimer_association':
        include __DIR__ . '/PHP/codes/supprimer_association.php';
        break;

    case 'ajout_association':
        include __DIR__ . '/PHP/codes/ajout_association.php';
        break;

    case 'modifier_association':
        include __DIR__ . '/PHP/codes/modifier_association.php';
        break;

    case 'messagerie':
        include __DIR__ . '/PHP/codes/messagerie.php';
        break;

    case 'envoyer_message':
        include __DIR__ . '/PHP/codes/envoyer_message.php';
        break;

    case 'exportation' :
    include __DIR__. '/PHP/codes/exportations.php';
    break;

    case 'exportations':
        include __DIR__ . '/PHP/codes/exportations.php';
        break;

    case 'export_excel_admin':
        include __DIR__ . '/PHP/codes/export_excel_admin.php';
        break;

    case 'export_calendar_admin':
        include __DIR__ . '/PHP/codes/export_calendar_admin.php';
        break;

        case 'telecharger_pdf':
        include __DIR__ . '/PHP/codes/telecharger_pdf.php';
        break;
}

ob_end_flush();





