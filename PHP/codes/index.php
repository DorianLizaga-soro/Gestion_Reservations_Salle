<?php



require_once("utilisateur.php");

$utilisateur1=new Utilisateur("Marie Dupont","marie.dupont@mairie-monastie.fr","Mairie","Gestionnaire");
$utilisateur2=new Utilisateur("Jean-Paul Fabre","jp.fabre@tennis-monastie.fr","Tennis","Responsable Association");
$utilisateur3=new Utilisateur("Sylvie Marque","sylvie.marque@lesarts.fr","Les Arts","Responsable Association");
$utilisateur4=new Utilisateur("Pierre Cazalet","p.cazalet@mairie-monastie.fr","Retraités","Responsable Association");
$utilisateur5=new Utilisateur("Lucie Vidal","l.vidal@petanque-monastie.fr","Pétanque","Responsable Association");
$utilisateur6=new Utilisateur("Ahmed Benali","a.benali@mairie-monastie.fr","Mairie","Personnel Ménage");
$utilisateur7=new Utilisateur("Isabelle Roux","i.roux@danse-monastie.fr","Danse","Responsable Association");
$utilisateur8=new Utilisateur("Thomas Blanc","t.blanc@mairie-monastie.fr","Mairie","Personnel Ménage");
$utilisateur9=new Utilisateur("Nathalie Pages","n.pages@yoga-monastie.fr","Yoga","Responsable Association");
$utilisateur10=new Utilisateur("Marc Soler","m.soler@chorale-monastie.fr","Chorale","Membre Association");

$utilisateurs=array($utilisateur1,$utilisateur2,$utilisateur3,$utilisateur4,$utilisateur5,
$utilisateur6,$utilisateur7,$utilisateur8,$utilisateur9,$utilisateur10);




?>