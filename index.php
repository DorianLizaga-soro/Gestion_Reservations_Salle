<?php
ob_start();
session_start();

require_once __DIR__ . '/PHP/codes/connexionBDD.php';



$page = $_GET['page'] ?? 'login';

switch($page){
    case 'login' :
        include __DIR__ . '/PHP/codes/login.php';
        break;
    
    case 'register' :
        include __DIR__ . '/PHP/codes/register.php';
        break;
    
    case 'menage' :
        include __DIR__ . '/PHP/codes/menage.php';
        break;

    case 'menagePersonnel':
        include __DIR__ . '/PHP/codes/menagePersonnel.php';
        break;

    case 'responsable' :
        include __DIR__ . '/PHP/codes/responsable.php';
        break;
    
    case 'membre' :
        include __DIR__ . '/PHP/codes/membre.php';
        break;
    
    case 'gestionnaire' :
        include __DIR__ . '/PHP/codes/gestionnaire.php';
        break;
    
    case 'logout' :
        include __DIR__ . '/PHP/codes/logout.php';
        break;
}

ob_end_flush();

?>